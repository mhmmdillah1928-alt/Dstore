<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Store;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'customers' => User::where('role', 'customer')->count(),
            'stores' => Store::where('is_verified', true)->count(),
            'pending_stores' => Store::where('is_verified', false)->count(),
            'products' => Product::count(),
            'orders' => Order::count(),
            'gmv' => Order::where('order_status', 'completed')->sum('total_amount'),
            'commission' => Order::where('order_status', 'completed')->sum('commission_amount'),
            'open_disputes' => Dispute::where('status', 'open')->count(),
            'pending_withdrawals' => Withdrawal::where('status', 'pending')->count(),
        ];
        return view('admin.dashboard', [
            'stats' => $stats,
            'recentOrders' => Order::with('customer', 'store')->latest()->take(8)->get(),
        ]);
    }

    public function stores()
    {
        return view('admin.stores', ['stores' => Store::with('user')->orderBy('is_verified')->latest()->paginate(15)]);
    }

    public function toggleStore(Store $store)
    {
        $store->update(['is_verified' => !$store->is_verified]);
        return back()->with('success', 'Status verifikasi toko diperbarui.');
    }

    public function products()
    {
        return view('admin.products', ['products' => Product::with('store', 'brand')->latest()->paginate(15)]);
    }

    public function toggleProduct(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);
        return back()->with('success', 'Status produk diperbarui.');
    }

    public function settings()
    {
        return view('admin.settings', ['commission' => Setting::get('commission_percent', 5)]);
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate(['commission_percent' => 'required|numeric|min:0|max:100']);
        Setting::set('commission_percent', $data['commission_percent']);
        return back()->with('success', 'Komisi diperbarui (berlaku untuk pesanan baru).');
    }

    public function withdrawals()
    {
        return view('admin.withdrawals', ['withdrawals' => Withdrawal::with('store')->latest()->paginate(15)]);
    }

    public function resolveWithdrawal(Request $request, Withdrawal $withdrawal)
    {
        $request->validate(['action' => 'required|in:approve,reject']);
        abort_unless($withdrawal->status === 'pending', 422);

        DB::transaction(function () use ($request, $withdrawal) {
            if ($request->action === 'approve') {
                $withdrawal->update(['status' => 'approved']);
            } else {
                $withdrawal->update(['status' => 'rejected']);
                $withdrawal->store()->increment('balance', $withdrawal->amount);
            }
        });

        return back()->with('success', 'Penarikan diproses.');
    }

    public function disputes()
    {
        return view('admin.disputes.index', ['disputes' => Dispute::with('order.store', 'user')->orderByRaw("status = 'open' desc")->latest()->paginate(15)]);
    }

    public function showDispute(Dispute $dispute)
    {
        $dispute->load('order.items.product', 'order.store', 'order.shipment', 'user');
        return view('admin.disputes.show', compact('dispute'));
    }

    public function resolveDispute(Request $request, Dispute $dispute)
    {
        $data = $request->validate(['action' => 'required|in:refund,reject', 'admin_note' => 'required|string']);
        abort_unless($dispute->status === 'open', 422);

        DB::transaction(function () use ($data, $dispute) {
            $order = $dispute->order;
            if ($data['action'] === 'refund') {
                foreach ($order->items as $i) {
                    $i->product()->increment('stock', $i->qty);
                }
                $order->update(['order_status' => 'cancelled']);
                $dispute->update(['status' => 'refunded', 'admin_note' => $data['admin_note']]);
            } else {
                $dispute->update(['status' => 'rejected', 'admin_note' => $data['admin_note']]);
            }
        });

        return redirect()->route('admin.disputes.index')->with('success', 'Sengketa diselesaikan.');
    }
}
