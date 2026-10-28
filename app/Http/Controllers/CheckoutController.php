<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function show(Request $request)
    {
        $items = $request->user()->cartItems()->with('product.images', 'product.store')->get();
        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong.');
        }
        return view('cart.checkout', ['items' => $items, 'total' => $items->sum(fn ($i) => $i->qty * $i->product->price)]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['shipping_address' => 'required|string|min:10']);
        $user = $request->user();
        $items = $user->cartItems()->with('product.store')->get();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong.');
        }

        $percent = (float) Setting::get('commission_percent', 5);

        try {
            DB::transaction(function () use ($items, $user, $data, $percent) {
                // Pecah menjadi satu order per toko
                foreach ($items->groupBy(fn ($i) => $i->product->store_id) as $storeId => $group) {
                    $total = 0;
                    $order = Order::create([
                        'order_number' => 'ORD-' . now()->format('ymd') . '-' . strtoupper(Str::random(6)),
                        'customer_id' => $user->id, 'store_id' => $storeId, 'total_amount' => 0,
                        'shipping_address' => $data['shipping_address'],
                    ]);

                    foreach ($group as $item) {
                        // Lock baris produk untuk mencegah stok minus
                        $product = Product::lockForUpdate()->find($item->product_id);
                        if (!$product->is_active || $product->stock < $item->qty) {
                            throw new \RuntimeException("Stok {$product->name} tidak mencukupi.");
                        }
                        $product->decrement('stock', $item->qty);
                        $order->items()->create([
                            'product_id' => $product->id, 'store_id' => $storeId,
                            'qty' => $item->qty, 'price' => $product->price,
                        ]);
                        $total += $item->qty * $product->price;
                    }

                    $order->update(['total_amount' => $total, 'commission_amount' => round($total * $percent / 100, 2)]);
                }
                $user->cartItems()->delete();
            });
        } catch (\RuntimeException $e) {
            return redirect()->route('cart.index')->with('error', $e->getMessage());
        }

        return redirect()->route('orders.index')->with('success', 'Pesanan dibuat. Silakan lakukan pembayaran.');
    }
}
