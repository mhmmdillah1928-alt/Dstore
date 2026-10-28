@extends('layouts.app')
@section('title', 'Pesanan Masuk')
@section('content')
<h4 class="mb-3">Pesanan Masuk</h4>
<div class="card shadow-sm"><div class="table-responsive">
<table class="table align-middle mb-0">
    <thead><tr><th>No. Order</th><th>Pembeli</th><th>Item</th><th>Total</th><th>Status</th><th></th></tr></thead>
    @forelse($orders as $order)
    <tr>
        <td>{{ $order->order_number }}<div class="small text-muted">{{ $order->created_at->format('d M Y H:i') }}</div></td>
        <td>{{ $order->customer->name }}</td>
        <td class="small">{{ $order->items->map(fn($i) => $i->product->name.' x'.$i->qty)->implode(', ') }}</td>
        <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
        <td>
            @include('layouts.status')
            @if($order->payment_review_status === 'pending_review')
                <span class="badge bg-info text-dark mt-1">Bukti perlu diperiksa</span>
            @elseif($order->payment_review_status === 'rejected')
                <span class="badge bg-danger mt-1">Bukti ditolak</span>
            @endif
        </td>
        <td><a href="{{ route('seller.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">Detail</a></td>
    </tr>
    @empty
    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada pesanan.</td></tr>
    @endforelse
</table></div></div>
<div class="mt-3">{{ $orders->links('pagination::bootstrap-5') }}</div>
@endsection
