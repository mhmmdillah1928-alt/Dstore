@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('content')
<h4 class="mb-3">Statistik Platform</h4>
<div class="row g-3 mb-4">
    @foreach([
        ['Pembeli', $stats['customers'], 'bi-people', 'primary'], ['Toko Aktif', $stats['stores'], 'bi-shop', 'success'],
        ['Toko Menunggu', $stats['pending_stores'], 'bi-hourglass-split', 'warning'], ['Produk', $stats['products'], 'bi-phone', 'info'],
        ['Total Order', $stats['orders'], 'bi-bag', 'primary'], ['GMV (selesai)', 'Rp '.number_format($stats['gmv'], 0, ',', '.'), 'bi-cash-stack', 'success'],
        ['Pendapatan Komisi', 'Rp '.number_format($stats['commission'], 0, ',', '.'), 'bi-percent', 'success'],
        ['Sengketa Terbuka', $stats['open_disputes'], 'bi-exclamation-triangle', 'danger'], ['Penarikan Pending', $stats['pending_withdrawals'], 'bi-wallet2', 'warning'],
    ] as [$l, $v, $ic, $c])
        <div class="col-6 col-lg-3"><div class="card shadow-sm border-0"><div class="card-body">
            <div class="text-{{ $c }}"><i class="bi {{ $ic }} fs-4"></i></div><div class="small text-muted">{{ $l }}</div><div class="fs-5 fw-bold">{{ $v }}</div>
        </div></div></div>
    @endforeach
</div>
<div class="card shadow-sm"><div class="card-header">Order Terbaru</div><div class="table-responsive">
<table class="table mb-0 align-middle">
    <tr><th>No</th><th>Pembeli</th><th>Toko</th><th>Total</th><th>Status</th></tr>
    @foreach($recentOrders as $order)
        <tr><td>{{ $order->order_number }}</td><td>{{ $order->customer->name }}</td><td>{{ $order->store->store_name }}</td>
            <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td><td>@include('layouts.status')</td></tr>
    @endforeach
</table></div></div>
@endsection
