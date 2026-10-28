@extends('layouts.app')
@section('title', $product->name)
@section('content')
<div class="product-detail">
<div class="row g-4 g-lg-5 align-items-start">
    <div class="col-md-6 col-lg-5">
        <div id="car" class="carousel slide card product-gallery overflow-hidden" data-bs-ride="carousel">
            <div class="carousel-inner">
                @forelse($product->images as $i => $img)
                    <div class="carousel-item {{ $i === 0 ? 'active' : '' }}"><img src="{{ asset('storage/'.$img->image_path) }}" class="d-block w-100" alt="{{ $product->name }}"></div>
                @empty
                    <div class="carousel-item active"><img src="{{ $product->thumbnail }}" class="d-block w-100" alt="{{ $product->name }}"></div>
                @endforelse
            </div>
            @if($product->images->count() > 1)
                <button class="carousel-control-prev" data-bs-target="#car" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
                <button class="carousel-control-next" data-bs-target="#car" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
            @endif
        </div>
    </div>
    <div class="col-md-6 col-lg-7">
        <div class="product-summary">
            <div class="d-flex flex-wrap gap-2 mb-3">
                <span class="badge {{ $product->condition === 'new' ? 'bg-success' : 'bg-warning text-dark' }}">{{ $product->condition_label }}</span>
                <span class="badge bg-secondary">{{ $product->brand->name }}</span>
            </div>
            <h1 class="product-detail-title">{{ $product->name }}</h1>
            @if($avgRating)<div class="product-detail-rating"><i class="bi bi-star-fill"></i> {{ number_format($avgRating, 1) }} <span class="text-muted">({{ $product->reviews->count() }} ulasan)</span></div>@endif
            <div class="product-detail-price">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
            <div class="product-specification">
                <div class="product-specification-heading">Spesifikasi utama</div>

                <table class="table table-sm product-specification-table mb-0">
                    <tbody>
                        <tr><th>RAM</th><td>{{ $product->ram }} GB</td><th>Storage</th><td>{{ $product->storage }} GB</td></tr>
                        <tr><th>Kondisi</th><td>{{ $product->condition === 'new' ? 'Baru (segel)' : 'Bekas' }}</td><th>Berat</th><td>{{ $product->weight }} gram</td></tr>
                        <tr><th>Stok</th><td colspan="3">{{ $product->stock }} unit</td></tr>
                    </tbody>
                </table>
            </div>

            @if(!auth()->check() || auth()->user()->role === 'customer')
                @if($product->stock > 0)
                    <form method="POST" action="{{ auth()->check() ? route('cart.add', $product) : route('login') }}" class="product-purchase-form">
                        @csrf
                        <label class="visually-hidden" for="product-quantity">Jumlah</label>
                        <input id="product-quantity" type="number" name="qty" value="1" min="1" max="{{ $product->stock }}" class="form-control">
                        <button class="btn btn-primary"><i class="bi bi-cart-plus me-1"></i>Tambah ke Keranjang</button>
                    </form>
                @else
                    <div class="alert alert-secondary">Stok habis</div>
                @endif
            @endif

            <div class="card product-store-card mt-4"><div class="card-body d-flex gap-3 align-items-center">
                <img src="{{ $product->store->logo_url }}" width="56" height="56" class="rounded-circle object-fit-cover" alt="Logo {{ $product->store->store_name }}">
                <div><div class="fw-bold">{{ $product->store->store_name }} <i class="bi bi-patch-check-fill text-primary" aria-label="Toko terverifikasi"></i></div>
                    <div class="small text-muted">{{ $product->store->address }}</div></div>
            </div></div>
        </div>
    </div>
</div>

<div class="card product-content-card mt-4"><div class="card-body">
    <h2 class="h5">Deskripsi</h2><p class="product-description mb-0">{{ $product->description ?: '-' }}</p>
</div></div>

<div class="card product-content-card mt-3"><div class="card-body">
    <h2 class="h5">Ulasan pelanggan</h2>
    @forelse($product->reviews as $r)
        <div class="border-bottom py-2">
            <strong>{{ $r->user->name }}</strong>
            <span class="text-warning">@for($i=1;$i<=5;$i++)<i class="bi bi-star{{ $i <= $r->rating ? '-fill' : '' }}"></i>@endfor</span>
            <div>{{ $r->comment }}</div>
        </div>
    @empty
        <p class="text-muted mb-0">Belum ada ulasan.</p>
    @endforelse
</div></div>
</div>
@endsection
