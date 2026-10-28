@extends('layouts.app')
@section('title', 'Pembayaran ' . $order->order_number)
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body p-4 p-md-5 text-center">
                <p class="section-eyebrow mb-2">Pembayaran QRIS</p>
                <h1 class="h4 fw-bold">Scan kode untuk membayar</h1>
                <p class="text-muted mb-1">Pesanan {{ $order->order_number }}</p>
                <div class="h3 fw-bold text-primary my-3">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>

                <div class="qris-image-frame mx-auto mb-4">
                    <img
                        src="{{ asset('images/payment/qris.png') }}"
                        class="img-fluid"
                        alt="Kode pembayaran QRIS"
                    >
                </div>

                <div class="text-start mx-auto qris-instructions">
                    <h2 class="h6 fw-bold">Cara membayar</h2>
                    <ol class="small text-muted mb-3">
                        <li>Buka aplikasi mobile banking atau e-wallet yang mendukung QRIS.</li>
                        <li>Scan kode QRIS di atas.</li>
                        <li>Masukkan nominal <strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong>, lalu selesaikan pembayaran.</li>
                    </ol>
                </div>

                @if($order->payment_review_status === 'pending_review')
                    <div class="alert alert-info mt-4 mb-0">
                        Bukti transfer sudah dikirim dan sedang menunggu verifikasi penjual. Anda tidak perlu mengirim ulang.
                    </div>
                @elseif($order->payment_review_status === 'approved')
                    <div class="alert alert-success mt-4 mb-0">
                        Pembayaran telah disetujui. Pesanan sedang menunggu diproses penjual.
                    </div>
                @else
                    @if($order->payment_review_status === 'rejected')
                        <div class="alert alert-danger mt-4 text-start">
                            <strong>Bukti pembayaran ditolak.</strong>
                            <div>{{ $order->payment_rejection_reason }}</div>
                            <div class="small mt-1">Silakan periksa nominal dan kirim bukti transfer yang benar.</div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('orders.payment-proof.store', $order) }}" enctype="multipart/form-data" class="text-start mx-auto qris-instructions mt-4">
                        @csrf
                        <label for="payment-proof" class="form-label fw-semibold">Unggah bukti transfer</label>
                        <input
                            id="payment-proof"
                            type="file"
                            name="payment_proof"
                            class="form-control @error('payment_proof') is-invalid @enderror"
                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                            required
                        >
                        @error('payment_proof')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Format JPG, PNG, atau WEBP. Maksimal 5 MB.</div>
                        <button type="submit" class="btn btn-primary w-100 mt-3">
                            <i class="bi bi-cloud-arrow-up me-1"></i>Kirim bukti untuk diperiksa
                        </button>
                    </form>
                @endif

                <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-primary mt-4">Kembali ke detail pesanan</a>
            </div>
        </div>
    </div>
</div>
@endsection
