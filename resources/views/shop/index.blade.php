@extends('layouts.app')
@section('title', 'Beranda')
@section('content')
<section class="shop-hero mb-4 mb-lg-5">
    <div class="hero-copy">
        <span class="hero-eyebrow"><i class="bi bi-stars me-1"></i>Temukan pilihan terbaik</span>
        <h1>Upgrade gadget,<br><span>tanpa ribet.</span></h1>
        <p>Jelajahi handphone pilihan dari toko terpercaya. Cari yang paling pas untuk kebutuhanmu.</p>
        <a href="#daftar-produk" class="btn btn-hero">Mulai jelajahi <i class="bi bi-arrow-down-right ms-1"></i></a>
    </div>
    <div class="hero-art" aria-hidden="true">
        <div class="hero-orbit hero-orbit-one"></div>
        <div class="hero-orbit hero-orbit-two"></div>
        <div class="hero-phone"><i class="bi bi-phone"></i></div>
        <span class="hero-spark hero-spark-one"><i class="bi bi-stars"></i></span>
        <span class="hero-spark hero-spark-two"><i class="bi bi-lightning-charge-fill"></i></span>
    </div>
</section>

<div class="row g-4 align-items-start">
    <aside class="col-lg-3">
        <details class="filter-card card">
            <summary class="filter-heading">
                <span><i class="bi bi-sliders me-2"></i>Filter produk</span>
                <i class="bi bi-chevron-down filter-chevron"></i>
            </summary>
            <form method="GET" class="filter-body">
                <div class="mb-3">
                    <label class="form-label" for="search-product">Cari handphone</label>
                    <div class="search-control">
                        <i class="bi bi-search"></i>
                        <input id="search-product" name="q" value="{{ request('q') }}" class="form-control" placeholder="Contoh: Galaxy S...">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="filter-brand">Brand</label>
                    <select id="filter-brand" name="brand" class="form-select">
                        <option value="">Semua brand</option>
                        @foreach($brands as $b)<option value="{{ $b->id }}" @selected(request('brand') == $b->id)>{{ $b->name }}</option>@endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="filter-condition">Kondisi</label>
                    <select id="filter-condition" name="condition" class="form-select">
                        <option value="">Semua kondisi</option>
                        <option value="new" @selected(request('condition') === 'new')>Baru</option>
                        <option value="used" @selected(request('condition') === 'used')>Bekas</option>
                    </select>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label" for="filter-ram">RAM</label>
                        <select id="filter-ram" name="ram" class="form-select">
                            <option value="">Semua</option>
                            @foreach($rams as $r)<option value="{{ $r }}" @selected(request('ram') == $r)>{{ $r }} GB</option>@endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="filter-storage">Storage</label>
                        <select id="filter-storage" name="storage" class="form-select">
                            <option value="">Semua</option>
                            @foreach($storages as $s)<option value="{{ $s }}" @selected(request('storage') == $s)>{{ $s }} GB</option>@endforeach
                        </select>
                    </div>
                </div>
                <fieldset class="mb-3">
                    <legend class="form-label">Rentang harga (Rp)</legend>
                    <div class="row g-2">
                        <div class="col-6"><input type="number" name="min_price" value="{{ request('min_price') }}" class="form-control" placeholder="Minimum" aria-label="Harga minimum"></div>
                        <div class="col-6"><input type="number" name="max_price" value="{{ request('max_price') }}" class="form-control" placeholder="Maksimum" aria-label="Harga maksimum"></div>
                    </div>
                </fieldset>
                <div class="mb-3">
                    <label class="form-label" for="filter-sort">Urutkan</label>
                    <select id="filter-sort" name="sort" class="form-select">
                        <option value="">Terbaru</option>
                        <option value="price_asc" @selected(request('sort') === 'price_asc')>Harga terendah</option>
                        <option value="price_desc" @selected(request('sort') === 'price_desc')>Harga tertinggi</option>
                    </select>
                </div>
                <button class="btn btn-primary w-100"><i class="bi bi-check2 me-1"></i>Terapkan filter</button>
                <a href="{{ route('home') }}" class="btn btn-filter-reset w-100 mt-2">Reset filter</a>
            </form>
        </details>
    </aside>

    <section class="col-lg-9" id="daftar-produk">
        <div class="product-section-heading">
            <div>
                <span class="section-eyebrow">Pilihan untukmu</span>
                <h2>Jelajahi handphone</h2>
            </div>
            <span class="product-count"><i class="bi bi-phone me-1"></i>{{ $products->total() }} produk</span>
        </div>
        <div class="row g-3 g-md-4">
            @forelse($products as $p)
                <div class="col-6 col-md-4">
                    <a href="{{ route('product.show', $p) }}" class="product-link">
                        <article class="card h-100 product-card">
                            <div class="product-image-wrap">
                                <img src="{{ $p->thumbnail }}" class="card-img-top" alt="{{ $p->name }}" loading="lazy">
                                <span class="badge product-condition {{ $p->condition === 'new' ? 'condition-new' : 'condition-used' }}">{{ $p->condition_label }}</span>
                            </div>
                            <div class="card-body">
                                <span class="product-brand">{{ $p->brand->name }}</span>
                                <h3 class="product-name">{{ $p->name }}</h3>
                                <div class="product-price">Rp {{ number_format($p->price, 0, ',', '.') }}</div>
                                <div class="product-spec">{{ $p->ram }}GB RAM <span>·</span> {{ $p->storage }}GB</div>
                                <div class="product-meta">
                                    <span class="product-store"><i class="bi bi-shop me-1"></i>{{ $p->store->store_name }}</span>
                                    @if($p->reviews_avg_rating)<span class="product-rating"><i class="bi bi-star-fill"></i> {{ number_format($p->reviews_avg_rating, 1) }}</span>@endif
                                </div>
                            </div>
                        </article>
                    </a>
                </div>
            @empty
                <div class="col-12">
                    <div class="empty-products">
                        <span class="empty-icon"><i class="bi bi-search"></i></span>
                        <h3>Produk belum ditemukan</h3>
                        <p>Coba ubah kata kunci atau filter pencarianmu.</p>
                        <a href="{{ route('home') }}" class="btn btn-outline-primary">Lihat semua produk</a>
                    </div>
                </div>
            @endforelse
        </div>
        <div class="mt-4">{{ $products->links('pagination::bootstrap-5') }}</div>
    </section>
</div>
@endsection
