@extends('layouts.app')
@section('title', 'Detail Sengketa')
@section('content')
@php $order = $dispute->order; @endphp
<a href="{{ route('admin.disputes.index') }}" class="small">&larr; Kembali</a>
<div class="row g-3 mt-1">
    <div class="col-md-7"><div class="card shadow-sm"><div class="card-body">
        <h5>{{ $order->order_number }} @include('layouts.status')</h5>
        <p class="mb-1">Pembeli: <strong>{{ $dispute->user->name }}</strong> &middot; Toko: <strong>{{ $order->store->store_name }}</strong></p>
        @foreach($order->items as $i)<div class="small">{{ $i->product->name }} x{{ $i->qty }} - Rp {{ number_format($i->price, 0, ',', '.') }}</div>@endforeach
        @if($order->shipment)<div class="small mt-1">Resi: {{ $order->shipment->courier_name }} {{ $order->shipment->tracking_number }}</div>@endif
        <hr><strong>Alasan pembeli:</strong><p style="white-space:pre-line">{{ $dispute->reason }}</p>
    </div></div></div>
    <div class="col-md-5">
        @if($dispute->status === 'open')
        <form method="POST" action="{{ route('admin.disputes.resolve', $dispute) }}" class="card card-body shadow-sm">
            @csrf
            <label class="form-label">Catatan admin</label>
            <textarea name="admin_note" rows="3" class="form-control mb-3" required></textarea>
            <div class="d-grid gap-2">
                <button name="action" value="refund" class="btn btn-success" onclick="return confirm('Batalkan order & kembalikan stok?')">Setujui Retur (Batalkan Order)</button>
                <button name="action" value="reject" class="btn btn-outline-danger">Tolak Sengketa</button>
            </div>
        </form>
        @else
            <div class="alert alert-secondary">Status: <strong>{{ $dispute->status }}</strong><br>{{ $dispute->admin_note }}</div>
        @endif
    </div>
</div>
@endsection
