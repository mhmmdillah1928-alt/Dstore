@extends('layouts.app')
@section('title', 'Moderasi Produk')
@section('content')
<h4 class="mb-3">Moderasi Produk</h4>
<div class="card shadow-sm"><div class="table-responsive">
<table class="table align-middle mb-0">
    <thead><tr><th>Produk</th><th>Toko</th><th>Harga</th><th>Status</th><th></th></tr></thead>
    @foreach($products as $p)
    <tr>
        <td><img src="{{ $p->thumbnail }}" width="40" class="rounded me-1"> {{ $p->name }} <span class="badge bg-secondary">{{ $p->brand->name }}</span></td>
        <td>{{ $p->store->store_name }}</td>
        <td>Rp {{ number_format($p->price, 0, ',', '.') }}</td>
        <td>@if($p->is_active)<span class="badge bg-success">Tayang</span>@else<span class="badge bg-danger">Diturunkan</span>@endif</td>
        <td><form method="POST" action="{{ route('admin.products.toggle', $p) }}">@csrf
            <button class="btn btn-sm {{ $p->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}">{{ $p->is_active ? 'Turunkan' : 'Tayangkan' }}</button></form></td>
    </tr>
    @endforeach
</table></div></div>
<div class="mt-3">{{ $products->links('pagination::bootstrap-5') }}</div>
@endsection
