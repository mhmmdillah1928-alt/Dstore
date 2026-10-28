<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = $request->user()->store->products()->with('images', 'brand')->latest()->paginate(10);
        return view('seller.products.index', compact('products'));
    }

    public function create()
    {
        return view('seller.products.form', ['product' => new Product(), 'brands' => Brand::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $product = $request->user()->store->products()->create($data + ['slug' => Str::slug($data['name']) . '-' . Str::lower(Str::random(5))]);
        $this->saveImages($request, $product);

        return redirect()->route('seller.products.index')->with('success', 'Produk ditambahkan.');
    }

    public function edit(Request $request, Product $product)
    {
        $this->own($request, $product);
        return view('seller.products.form', ['product' => $product->load('images'), 'brands' => Brand::orderBy('name')->get()]);
    }

    public function update(Request $request, Product $product)
    {
        $this->own($request, $product);
        $product->update($this->validated($request));
        $this->saveImages($request, $product);

        return redirect()->route('seller.products.index')->with('success', 'Produk diperbarui.');
    }

    public function destroy(Request $request, Product $product)
    {
        $this->own($request, $product);
        if ($product->orderItemsExist()) {
            // Punya riwayat transaksi: nonaktifkan saja
            $product->update(['is_active' => false, 'stock' => 0]);
            return back()->with('success', 'Produk memiliki riwayat transaksi, sehingga dinonaktifkan.');
        }
        foreach ($product->images as $img) {
            Storage::disk('public')->delete($img->image_path);
        }
        $product->delete();
        return back()->with('success', 'Produk dihapus.');
    }

    public function destroyImage(Request $request, ProductImage $image)
    {
        $this->own($request, $image->product);
        Storage::disk('public')->delete($image->image_path);
        $image->delete();
        return back()->with('success', 'Foto dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'name' => 'required|string|max:255',
            'condition' => 'required|in:new,used',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'weight' => 'required|integer|min:0',
            'ram' => 'required|integer|min:1',
            'storage' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'images.*' => 'nullable|image|max:2048',
        ]);
    }

    private function saveImages(Request $request, Product $product): void
    {
        foreach ($request->file('images', []) as $file) {
            $product->images()->create(['image_path' => $file->store('products', 'public')]);
        }
    }

    private function own(Request $request, Product $product): void
    {
        abort_unless($product->store_id === $request->user()->store->id, 403);
    }
}
