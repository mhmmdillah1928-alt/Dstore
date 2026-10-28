<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MarketplaceFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_purchase_flow(): void
    {
        $this->seed(DatabaseSeeder::class);
        $customer = User::where('email', 'customer@mail.com')->first();
        $seller = User::where('email', 'seller@mail.com')->first();
        $admin = User::where('email', 'admin@mail.com')->first();
        $product = Product::first();
        $stock = $product->stock;

        $this->get('/?brand=' . $product->brand_id . '&condition=' . $product->condition)->assertOk()->assertSee($product->name);
        $this->get('/produk/' . $product->slug)->assertOk();

        $this->actingAs($customer)->post('/keranjang/' . $product->id, ['qty' => 2])->assertRedirect('/keranjang');
        $this->actingAs($customer)->get('/keranjang')->assertOk();
        $this->actingAs($customer)->get('/checkout')->assertOk();
        $this->actingAs($customer)->post('/checkout', ['shipping_address' => 'Jl. Test No. 1 Jakarta'])->assertRedirect('/pesanan');

        $order = Order::first();
        $this->assertEquals($stock - 2, $product->fresh()->stock);
        $this->assertEquals(2 * $product->price * 0.05, (float) $order->commission_amount);

        $this->actingAs($customer)
            ->get(route('orders.payment', $order))
            ->assertOk()
            ->assertSee('Kode pembayaran QRIS')
            ->assertSee('Masukkan nominal')
            ->assertSee('Unggah bukti transfer');
        $this->assertEquals('pending', $order->fresh()->payment_status);

        $order->update(['payment_status' => 'paid']);
        $this->assertEquals('paid', $order->fresh()->payment_status);
        $this->actingAs($seller)->get('/seller/pesanan')->assertOk();
        $this->actingAs($seller)->post("/seller/pesanan/{$order->id}/konfirmasi");
        $this->actingAs($seller)->post("/seller/pesanan/{$order->id}/kirim", ['courier_name' => 'JNE', 'tracking_number' => 'JNE123', 'shipping_cost' => null]);
        $this->assertDatabaseHas('shipments', ['order_id' => $order->id, 'shipping_cost' => 0]);
        $this->assertEquals('shipped', $order->fresh()->order_status);

        $this->actingAs($customer)->get("/pesanan/{$order->id}")->assertOk()->assertSee('JNE123');
        $this->actingAs($customer)->post("/pesanan/{$order->id}/selesai");
        $this->assertEquals('completed', $order->fresh()->order_status);
        $this->assertEquals($order->total_amount - $order->commission_amount, (float) $seller->store->fresh()->balance);

        $item = $order->items()->first();
        $this->actingAs($customer)->post("/ulasan/{$item->id}", ['rating' => 5, 'comment' => 'Mantap']);
        $this->assertDatabaseHas('reviews', ['product_id' => $product->id, 'rating' => 5]);

        $this->actingAs($seller)->post('/seller/saldo', ['amount' => 20000]);
        $this->assertDatabaseHas('withdrawals', ['amount' => 20000, 'status' => 'pending']);
        $this->actingAs($admin)->get('/admin')->assertOk();
        $this->actingAs($admin)->get('/admin/penarikan')->assertOk();
    }

    public function test_roles_are_enforced_and_seller_registration_needs_verification(): void
    {
        $this->seed(DatabaseSeeder::class);
        $customer = User::where('email', 'customer@mail.com')->first();
        $this->actingAs($customer)->get('/admin')->assertForbidden();
        $this->actingAs($customer)->get('/seller/produk')->assertNotFound();

        auth()->logout();
        $this->post('/register', [
            'name' => 'Toko Baru', 'email' => 'new@mail.com', 'phone' => '0812', 'password' => 'password123',
            'password_confirmation' => 'password123', 'role' => 'seller', 'store_name' => 'Toko Baru', 'address' => 'Alamat',
        ])->assertSessionHasNoErrors()->assertRedirect('/seller');
        $this->get('/seller/products')->assertRedirect('/seller');
        $this->get('/seller')->assertOk();
    }

    public function test_qris_payment_page_does_not_change_payment_status(): void
    {
        $this->seed(DatabaseSeeder::class);
        $product = Product::first();
        $order = $this->makeOrderForProduct($product);

        $this->actingAs(User::where('email', 'customer@mail.com')->first())
            ->get(route('orders.payment', $order))
            ->assertOk()
            ->assertSee('images/payment/qris.png')
            ->assertSee('Rp ' . number_format($order->total_amount, 0, ',', '.'))
            ->assertSee('Kirim bukti untuk diperiksa');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'payment_status' => 'pending',
        ]);
    }

    public function test_customer_can_submit_payment_proof_for_seller_approval(): void
    {
        Storage::fake('local');
        $this->seed(DatabaseSeeder::class);
        $order = $this->makeOrderForProduct(Product::first());
        $customer = User::where('email', 'customer@mail.com')->first();
        $seller = User::where('email', 'seller@mail.com')->first();

        $this->actingAs($customer)
            ->post(route('orders.payment-proof.store', $order), [
                'payment_proof' => UploadedFile::fake()->image('transfer.png'),
            ])
            ->assertRedirect();

        $order->refresh();
        Storage::disk('local')->assertExists($order->payment_proof_path);
        $this->assertSame('pending_review', $order->payment_review_status);
        $this->assertSame('pending', $order->payment_status);

        $this->actingAs($seller)
            ->get(route('seller.orders.show', $order))
            ->assertOk()
            ->assertSee('Verifikasi Pembayaran');
        $this->actingAs($seller)
            ->get(route('seller.orders.payment-proof', $order))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png')
            ->assertHeader('Content-Disposition', 'inline');
        $this->actingAs($seller)
            ->post(route('seller.orders.payment.approve', $order))
            ->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'payment_status' => 'paid',
            'payment_review_status' => 'approved',
        ]);
        $this->actingAs($seller)
            ->post(route('seller.orders.confirm', $order))
            ->assertRedirect();
        $this->assertSame('processing', $order->fresh()->order_status);
    }

    public function test_rejected_payment_proof_can_be_replaced_by_customer(): void
    {
        Storage::fake('local');
        $this->seed(DatabaseSeeder::class);
        $order = $this->makeOrderForProduct(Product::first());
        $customer = User::where('email', 'customer@mail.com')->first();
        $seller = User::where('email', 'seller@mail.com')->first();

        $this->actingAs($customer)->post(route('orders.payment-proof.store', $order), [
            'payment_proof' => UploadedFile::fake()->image('first-proof.png'),
        ]);
        $oldProofPath = $order->fresh()->payment_proof_path;

        $this->actingAs($seller)
            ->post(route('seller.orders.payment.reject', $order), [
                'payment_rejection_reason' => 'Nominal transfer tidak sesuai.',
            ])
            ->assertRedirect();

        $this->assertSame('rejected', $order->fresh()->payment_review_status);
        $this->assertSame('pending', $order->fresh()->payment_status);
        $this->actingAs($customer)
            ->get(route('orders.payment', $order))
            ->assertOk()
            ->assertSee('Nominal transfer tidak sesuai.');

        $this->actingAs($customer)->post(route('orders.payment-proof.store', $order), [
            'payment_proof' => UploadedFile::fake()->image('replacement-proof.png'),
        ])->assertRedirect();

        $order->refresh();
        $this->assertSame('pending_review', $order->payment_review_status);
        $this->assertNull($order->payment_rejection_reason);
        $this->assertNotSame($oldProofPath, $order->payment_proof_path);
        Storage::disk('local')->assertMissing($oldProofPath);
        Storage::disk('local')->assertExists($order->payment_proof_path);
    }

    private function makeOrderForProduct(Product $product): Order
    {
        $customer = User::where('email', 'customer@mail.com')->first();
        $order = Order::create([
            'order_number' => 'ORD-TEST-' . uniqid(),
            'customer_id' => $customer->id,
            'store_id' => $product->store_id,
            'total_amount' => $product->price,
            'shipping_address' => 'Jl. Test No. 1 Jakarta',
        ]);
        $order->items()->create([
            'product_id' => $product->id,
            'store_id' => $product->store_id,
            'qty' => 1,
            'price' => $product->price,
        ]);

        return $order;
    }
}
