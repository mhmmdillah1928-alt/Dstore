<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $store = $request->user()->store;
        $stats = null;
        if ($store?->is_verified) {
            $stats = [
                'products' => Product::where('store_id', $store->id)->count(),
                'new_orders' => Order::where('store_id', $store->id)->where('payment_status', 'paid')->where('order_status', 'pending')->count(),
                'shipping' => Order::where('store_id', $store->id)->where('order_status', 'shipped')->count(),
                'balance' => $store->balance,
            ];
        }
        return view('seller.dashboard', compact('store', 'stats'));
    }

    public function editStore(Request $request)
    {
        return view('seller.store', ['store' => $request->user()->store]);
    }

    public function updateStore(Request $request)
    {
        $store = $request->user()->store;
        $data = $request->validate([
            'store_name' => 'required|string|max:255',
            'address' => 'required|string',
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($store->logo) {
                Storage::disk('public')->delete($store->logo);
            }
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }
        $store->update($data);

        return back()->with('success', 'Profil toko diperbarui.');
    }
}
