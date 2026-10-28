@extends('layouts.app')
@section('title', 'Keranjang')
@section('content')
<h4 class="mb-3">Keranjang Belanja</h4>
@if($items->isEmpty())
    <div class="alert alert-info">Keranjang kosong. <a href="{{ route('home') }}">Mulai belanja</a></div>
@else
<div class="card shadow-sm"><div class="table-responsive">
    <table class="table align-middle mb-0">
        <thead><tr><th>Produk</th><th>Harga</th><th style="width:150px">Qty</th><th>Subtotal</th><th></th></tr></thead>
        @foreach($items as $item)
        <tr>
            <td><div class="d-flex gap-2 align-items-center"><img src="{{ $item->product->thumbnail }}" width="56" class="rounded">
                <div><a href="{{ route('product.show', $item->product) }}">{{ $item->product->name }}</a><div class="small text-muted">{{ $item->product->store->store_name }}</div></div></div></td>
            <td>Rp {{ number_format($item->product->price, 0, ',', '.') }}</td>
            <td><form method="POST" action="{{ route('cart.update', $item) }}" class="d-flex gap-1">@csrf @method('PATCH')
                <input type="number" name="qty" value="{{ $item->qty }}" min="1" max="{{ $item->product->stock }}" class="form-control form-control-sm">
                <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-check"></i></button></form></td>
            <td>Rp {{ number_format($item->qty * $item->product->price, 0, ',', '.') }}</td>
            <td><form method="POST" action="{{ route('cart.remove', $item) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form></td>
        </tr>
        @endforeach
    </table>
</div></div>
<div class="d-flex justify-content-between align-items-center mt-3">
    <h5>Total: Rp {{ number_format($total, 0, ',', '.') }}</h5>
    <a href="{{ route('checkout.show') }}" class="btn btn-primary">Checkout</a>
</div>
@endif
@endsection
