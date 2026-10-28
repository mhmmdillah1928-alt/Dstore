@extends('layouts.app')
@section('title', $order->order_number)
@section('content')
<a href="{{ route('orders.index') }}" class="small">&larr; Kembali</a>
<div class="d-flex justify-content-between align-items-center my-2">
    <h4>{{ $order->order_number }}</h4><div>@include('layouts.status')</div>
</div>
@if($order->payment_review_status === 'pending_review')
    <div class="alert alert-info py-2">Bukti transfer sedang diperiksa oleh penjual.</div>
@elseif($order->payment_review_status === 'approved')
    <div class="alert alert-success py-2">Pembayaran telah disetujui oleh penjual.</div>
@elseif($order->payment_review_status === 'rejected')
    <div class="alert alert-danger py-2">
        <strong>Bukti transfer ditolak.</strong> {{ $order->payment_rejection_reason }}
        <div class="small mt-1">Buka pembayaran QRIS untuk mengirim bukti yang baru.</div>
    </div>
@endif

<div class="row g-3">
    <div class="col-md-8">
        <div class="card shadow-sm mb-3"><div class="card-body">
            <h6>Item dari {{ $order->store->store_name }}</h6>
            @foreach($order->items as $item)
                <div class="border-bottom py-2">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex gap-2 align-items-center"><img src="{{ $item->product->thumbnail }}" width="50" class="rounded">
                            <a href="{{ route('product.show', $item->product) }}">{{ $item->product->name }}</a></div>
                        <div>{{ $item->qty }} x Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                    </div>
                    @if($order->order_status === 'completed')
                        @if($item->review)
                            <div class="small text-warning mt-1">@for($i=1;$i<=5;$i++)<i class="bi bi-star{{ $i <= $item->review->rating ? '-fill' : '' }}"></i>@endfor
                                <span class="text-muted">{{ $item->review->comment }}</span></div>
                        @else
                            <form method="POST" action="{{ route('orders.review', $item) }}" class="row g-2 mt-1">
                                @csrf
                                <div class="col-auto"><select name="rating" class="form-select form-select-sm">
                                    @for($i=5;$i>=1;$i--)<option value="{{ $i }}">{{ $i }} ★</option>@endfor</select></div>
                                <div class="col"><input name="comment" class="form-control form-control-sm" placeholder="Tulis ulasan..."></div>
                                <div class="col-auto"><button class="btn btn-sm btn-outline-primary">Kirim</button></div>
                            </form>
                        @endif
                    @endif
                </div>
            @endforeach
            <div class="text-end fw-bold mt-2">Total: Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
        </div></div>

        @if($order->dispute)
            <div class="alert alert-warning"><strong>Sengketa: {{ ['open' => 'Menunggu admin', 'refunded' => 'Disetujui (refund)', 'rejected' => 'Ditolak'][$order->dispute->status] }}</strong>
                <div>{{ $order->dispute->reason }}</div>
                @if($order->dispute->admin_note)<div class="small">Catatan admin: {{ $order->dispute->admin_note }}</div>@endif</div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm mb-3"><div class="card-body">
            <h6>Pengiriman</h6>
            <p class="small mb-2" style="white-space:pre-line">{{ $order->shipping_address }}</p>
            @if($order->shipment)
                <div class="small"><strong>{{ $order->shipment->courier_name }}</strong><br>Resi: <code>{{ $order->shipment->tracking_number }}</code></div>
            @else
                <div class="small text-muted">Belum ada nomor resi.</div>
            @endif
        </div></div>

        <div class="d-grid gap-2">
            @if($order->order_status === 'pending' && $order->payment_status === 'pending')
                @if(in_array($order->payment_review_status, ['not_submitted', 'rejected'], true))
                    <a href="{{ route('orders.payment', $order) }}" class="btn btn-success w-100">
                        {{ $order->payment_review_status === 'rejected' ? 'Perbaiki Bukti Pembayaran' : 'Bayar dengan QRIS' }}
                    </a>
                @endif
                <form method="POST" action="{{ route('orders.cancel', $order) }}" onsubmit="return confirm('Batalkan pesanan?')">@csrf<button class="btn btn-outline-danger w-100">Batalkan</button></form>
            @endif
            @if($order->order_status === 'shipped')
                @if(!$order->dispute || $order->dispute->status !== 'open')
                    <form method="POST" action="{{ route('orders.complete', $order) }}" onsubmit="return confirm('Barang sudah diterima dengan baik?')">@csrf<button class="btn btn-primary w-100">Pesanan Diterima</button></form>
                @endif
                @if(!$order->dispute)
                    <form method="POST" action="{{ route('orders.dispute', $order) }}" class="card card-body">
                        @csrf<label class="form-label small">Ajukan retur / sengketa</label>
                        <textarea name="reason" rows="2" class="form-control form-control-sm mb-2" required minlength="10" placeholder="Jelaskan masalahnya"></textarea>
                        <button class="btn btn-sm btn-outline-warning">Ajukan</button>
                    </form>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
