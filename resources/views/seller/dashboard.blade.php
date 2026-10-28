@extends('layouts.app')
@section('title', 'Dashboard Seller')
@section('content')
<h4 class="mb-3">Dashboard Toko: {{ $store->store_name }}</h4>
@if(!$store->is_verified)
    <div class="alert alert-warning"><i class="bi bi-hourglass-split"></i> Toko Anda sedang menunggu verifikasi admin. Anda dapat melengkapi <a href="{{ route('seller.store.edit') }}">profil toko</a> sementara menunggu.</div>
@else
<div class="row g-3">
    @foreach([['Produk', $stats['products'], 'bi-phone', 'primary'], ['Pesanan Baru (dibayar)', $stats['new_orders'], 'bi-bag-check', 'warning'], ['Sedang Dikirim', $stats['shipping'], 'bi-truck', 'info'], ['Saldo', 'Rp '.number_format($stats['balance'], 0, ',', '.'), 'bi-wallet2', 'success']] as [$l, $v, $ic, $c])
        <div class="col-6 col-lg-3"><div class="card shadow-sm border-0"><div class="card-body">
            <div class="text-{{ $c }}"><i class="bi {{ $ic }} fs-3"></i></div><div class="text-muted small">{{ $l }}</div><div class="fs-5 fw-bold">{{ $v }}</div>
        </div></div></div>
    @endforeach
</div>
@endif
@endsection
