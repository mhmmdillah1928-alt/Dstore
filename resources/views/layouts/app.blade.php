<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f7f8fc">
    <title>@yield('title', config('app.name')) - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/store.css') }}" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light site-navbar">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ route('home') }}">
            <span class="brand-icon"><i class="bi bi-phone"></i></span>
            <span>Dstore<span class="brand-dot">.</span></span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-expanded="false" aria-label="Buka navigasi">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav me-auto">
                @auth
                    @if(auth()->user()->role === 'admin')
                        <li class="nav-item"><a @class(['nav-link', 'active' => request()->routeIs('admin.dashboard')]) href="{{ route('admin.dashboard') }}" @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif>Dashboard</a></li>
                        <li class="nav-item"><a @class(['nav-link', 'active' => request()->routeIs('admin.stores')]) href="{{ route('admin.stores') }}" @if(request()->routeIs('admin.stores')) aria-current="page" @endif>Toko</a></li>
                        <li class="nav-item"><a @class(['nav-link', 'active' => request()->routeIs('admin.products')]) href="{{ route('admin.products') }}" @if(request()->routeIs('admin.products')) aria-current="page" @endif>Produk</a></li>
                        <li class="nav-item"><a @class(['nav-link', 'active' => request()->routeIs('admin.disputes.*')]) href="{{ route('admin.disputes.index') }}" @if(request()->routeIs('admin.disputes.*')) aria-current="page" @endif>Sengketa</a></li>
                        <li class="nav-item"><a @class(['nav-link', 'active' => request()->routeIs('admin.withdrawals')]) href="{{ route('admin.withdrawals') }}" @if(request()->routeIs('admin.withdrawals')) aria-current="page" @endif>Penarikan</a></li>
                        <li class="nav-item"><a @class(['nav-link', 'active' => request()->routeIs('admin.settings')]) href="{{ route('admin.settings') }}" @if(request()->routeIs('admin.settings')) aria-current="page" @endif>Komisi</a></li>
                    @elseif(auth()->user()->role === 'seller')
                        <li class="nav-item"><a @class(['nav-link', 'active' => request()->routeIs('seller.dashboard')]) href="{{ route('seller.dashboard') }}" @if(request()->routeIs('seller.dashboard')) aria-current="page" @endif>Dashboard</a></li>
                        <li class="nav-item"><a @class(['nav-link', 'active' => request()->routeIs('seller.products.*')]) href="{{ route('seller.products.index') }}" @if(request()->routeIs('seller.products.*')) aria-current="page" @endif>Produk</a></li>
                        <li class="nav-item"><a @class(['nav-link', 'active' => request()->routeIs('seller.orders.*')]) href="{{ route('seller.orders.index') }}" @if(request()->routeIs('seller.orders.*')) aria-current="page" @endif>Pesanan</a></li>
                        <li class="nav-item"><a @class(['nav-link', 'active' => request()->routeIs('seller.withdrawals.*')]) href="{{ route('seller.withdrawals.index') }}" @if(request()->routeIs('seller.withdrawals.*')) aria-current="page" @endif>Saldo</a></li>
                        <li class="nav-item"><a @class(['nav-link', 'active' => request()->routeIs('seller.store.*')]) href="{{ route('seller.store.edit') }}" @if(request()->routeIs('seller.store.*')) aria-current="page" @endif>Profil Toko</a></li>
                    @else
                        <li class="nav-item"><a @class(['nav-link', 'active' => request()->routeIs('home', 'product.show')]) href="{{ route('home') }}" @if(request()->routeIs('home', 'product.show')) aria-current="page" @endif>Belanja</a></li>
                        <li class="nav-item"><a @class(['nav-link', 'active' => request()->routeIs('orders.*')]) href="{{ route('orders.index') }}" @if(request()->routeIs('orders.*')) aria-current="page" @endif>Pesanan Saya</a></li>
                    @endif
                @else
                    <li class="nav-item"><a @class(['nav-link', 'active' => request()->routeIs('home', 'product.show')]) href="{{ route('home') }}" @if(request()->routeIs('home', 'product.show')) aria-current="page" @endif>Belanja</a></li>
                @endauth
            </ul>
            <ul class="navbar-nav align-items-center">
                @auth
                    @if(auth()->user()->role === 'customer')
                        <li class="nav-item"><a @class(['nav-link', 'nav-cart', 'active' => request()->routeIs('cart.*', 'checkout.*')]) href="{{ route('cart.index') }}" @if(request()->routeIs('cart.*', 'checkout.*')) aria-current="page" @endif><i class="bi bi-cart3"></i> Keranjang</a></li>
                    @endif
                    <li class="nav-item"><span class="nav-link nav-user"><i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }} <span class="text-capitalize">({{ auth()->user()->role }})</span></span></li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-sm btn-nav-logout">Keluar</button></form>
                    </li>
                @else
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Masuk</a></li>
                    <li class="nav-item ms-lg-2"><a class="btn btn-sm btn-nav-register" href="{{ route('register') }}">Daftar</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<main class="container site-main py-4 py-lg-5">
    @foreach(['success', 'error'] as $f)
        @if(session($f))
            <div class="alert alert-{{ $f === 'error' ? 'danger' : 'success' }} alert-dismissible fade show">{{ session($f) }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
    @endforeach
    @if($errors->any() && !isset($hideErrors))
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    @yield('content')
</main>

<footer class="site-footer text-center py-4 small">
    <div class="container">&copy; {{ date('Y') }} Dstore <span class="mx-1">·</span> Marketplace pilihan untuk handphone favoritmu.</div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
