<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $q = Product::with(['images', 'brand', 'store'])
            ->withAvg('reviews', 'rating')
            ->where('is_active', true)
            ->whereHas('store', fn ($s) => $s->where('is_verified', true));

        $q->when($request->q, fn ($x, $v) => $x->where('name', 'like', "%$v%"))
          ->when($request->brand, fn ($x, $v) => $x->where('brand_id', $v))
          ->when($request->condition, fn ($x, $v) => $x->where('condition', $v))
          ->when($request->ram, fn ($x, $v) => $x->where('ram', $v))
          ->when($request->storage, fn ($x, $v) => $x->where('storage', $v))
          ->when($request->min_price, fn ($x, $v) => $x->where('price', '>=', $v))
          ->when($request->max_price, fn ($x, $v) => $x->where('price', '<=', $v));

        match ($request->sort) {
            'price_asc' => $q->orderBy('price'),
            'price_desc' => $q->orderByDesc('price'),
            default => $q->latest(),
        };

        return view('shop.index', [
            'products' => $q->paginate(12)->withQueryString(),
            'brands' => Brand::orderBy('name')->get(),
            'rams' => Product::distinct()->orderBy('ram')->pluck('ram'),
            'storages' => Product::distinct()->orderBy('storage')->pluck('storage'),
        ]);
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active && $product->store->is_verified, 404);
        $product->load(['images', 'brand', 'store', 'reviews.user']);

        return view('shop.show', [
            'product' => $product,
            'avgRating' => $product->reviews->avg('rating'),
        ]);
    }
}
