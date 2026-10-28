@extends('layouts.app')
@section('title', $order->order_number)
@section('content')
<a href="{{ route('seller.orders.index') }}" class="small">&larr; Kembali</a>
<div class="d-flex justify-content-between align-items-center my-2"><h4>{{ $order->order_number }}</h4><div>@include('layouts.status')</div></div>
<div class="row g-3">
    <div class="col-md-8"><div class="card shadow-sm"><div class="card-body">
        @foreach($order->items as $item)
            <div class="d-flex justify-content-between border-bottom py-2"><span>{{ $item->product->name }}</span><span>{{ $item->qty }} x Rp {{ number_format($item->price, 0, ',', '.') }}</span></div>
        @endforeach
        <div class="text-end mt-2">Total: <strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong></div>
        <div class="text-end small text-muted">Komisi platform: Rp {{ number_format($order->commission_amount, 0, ',', '.') }} &middot; Diterima: Rp {{ number_format($order->total_amount - $order->commission_amount, 0, ',', '.') }}</div>
        @if($order->dispute)<div class="alert alert-warning mt-3 mb-0">Sengketa ({{ $order->dispute->status }}): {{ $order->dispute->reason }}</div>@endif
    </div></div></div>
    <div class="col-md-4">
        <div class="card shadow-sm mb-3"><div class="card-body">
            <h6>Pembeli</h6><div>{{ $order->customer->name }} &middot; {{ $order->customer->phone }}</div>
            <p class="small mt-2 mb-0" style="white-space:pre-line">{{ $order->shipping_address }}</p>
        </div></div>
        @if($order->order_status === 'pending')
            @if($order->payment_status === 'paid')
                <form method="POST" action="{{ route('seller.orders.confirm', $order) }}">@csrf<button class="btn btn-primary w-100">Konfirmasi Pesanan</button></form>
            @elseif($order->payment_review_status === 'pending_review' && $order->payment_proof_path)
                <div class="card card-body">
                    <h6 class="mb-1">Verifikasi Pembayaran</h6>
                    <p class="small text-muted">Customer mengirim bukti transfer. Periksa bukti dan nominal sebelum menyetujui.</p>
                    <a href="{{ route('seller.orders.payment-proof', $order) }}" target="_blank" rel="noopener" class="d-block mb-3">
                        <img src="{{ route('seller.orders.payment-proof', $order) }}" alt="Bukti transfer customer" class="img-fluid rounded border">
                    </a>
                    <form method="POST" action="{{ route('seller.orders.payment.approve', $order) }}" class="mb-2" onsubmit="return confirm('Setujui pembayaran ini?')">
                        @csrf
                        <button class="btn btn-success w-100"><i class="bi bi-check-circle me-1"></i>Setujui Pembayaran</button>
                    </form>
                    <form method="POST" action="{{ route('seller.orders.payment.reject', $order) }}" onsubmit="return confirm('Tolak bukti pembayaran ini?')">
                        @csrf
                        <label for="payment-rejection-reason" class="form-label small">Alasan penolakan</label>
                        <textarea id="payment-rejection-reason" name="payment_rejection_reason" rows="2" minlength="5" maxlength="1000" class="form-control form-control-sm mb-2" placeholder="Contoh: Nominal transfer tidak sesuai" required></textarea>
                        <button class="btn btn-outline-danger w-100">Tolak Bukti Pembayaran</button>
                    </form>
                </div>
            @elseif($order->payment_review_status === 'rejected')
                <div class="alert alert-danger mb-0">
                    <strong>Bukti transfer ditolak.</strong>
                    <div class="small mt-1">{{ $order->payment_rejection_reason }}</div>
                    <div class="small mt-1">Menunggu customer mengirim bukti baru.</div>
                </div>
            @else
                <div class="alert alert-secondary">Menunggu customer membayar dan mengirim bukti transfer.</div>
            @endif
        @elseif($order->order_status === 'processing')
            <form method="POST" action="{{ route('seller.orders.ship', $order) }}" class="card card-body">
                @csrf <h6>Input Resi Pengiriman</h6>
                <input name="courier_name" class="form-control form-control-sm mb-2" placeholder="Kurir (JNE, J&T, ...)" required>
                <input name="tracking_number" class="form-control form-control-sm mb-2" placeholder="Nomor resi" required>
                <label for="shipping-cost" class="form-label small mb-1">Ongkos kirim <span class="text-muted">(opsional)</span></label>
                <input id="shipping-cost" type="number" name="shipping_cost" value="{{ old('shipping_cost') }}" min="0" step="0.01" class="form-control form-control-sm mb-2" placeholder="Kosongkan jika Rp 0">
                <button class="btn btn-primary btn-sm">Kirim Pesanan</button>
            </form>
        @elseif($order->shipment)
            <div class="card card-body small"><strong>{{ $order->shipment->courier_name }}</strong> Resi: <code>{{ $order->shipment->tracking_number }}</code></div>
        @endif
    </div>
</div>
@endsection
