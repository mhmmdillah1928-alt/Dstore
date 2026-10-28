<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $items = $request->user()->cartItems()->with('product.images', 'product.store')->get();
        return view('cart.index', ['items' => $items, 'total' => $items->sum(fn ($i) => $i->qty * $i->product->price)]);
    }

    public function add(Request $request, Product $product)
    {
        $request->validate(['qty' => 'nullable|integer|min:1']);
        abort_unless($product->is_active && $product->store->is_verified, 404);

        if ($product->store->user_id === $request->user()->id) {
            return back()->with('error', 'Tidak bisa membeli produk toko sendiri.');
        }

        $item = CartItem::firstOrNew(['user_id' => $request->user()->id, 'product_id' => $product->id]);
        $item->qty = min(($item->qty ?? 0) + ($request->qty ?? 1), $product->stock);
        if ($item->qty < 1) {
            return back()->with('error', 'Stok habis.');
        }
        $item->save();

        return redirect()->route('cart.index')->with('success', 'Produk ditambahkan ke keranjang.');
    }

    public function update(Request $request, CartItem $item)
    {
        abort_unless($item->user_id === $request->user()->id, 403);
        $request->validate(['qty' => 'required|integer|min:1']);
        $item->update(['qty' => min($request->qty, $item->product->stock)]);
        return back()->with('success', 'Keranjang diperbarui.');
    }

    public function remove(Request $request, CartItem $item)
    {
        abort_unless($item->user_id === $request->user()->id, 403);
        $item->delete();
        return back()->with('success', 'Item dihapus.');
    }
}
