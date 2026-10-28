@extends('layouts.app')
@section('title', 'Checkout')
@section('content')
<h4 class="mb-3">Checkout</h4>
<div class="row g-4">
    <div class="col-md-7">
        <div class="card shadow-sm"><div class="card-body">
            <form method="POST" action="{{ route('checkout.store') }}">
                @csrf
                <label class="form-label">Alamat Pengiriman</label>
                <textarea name="shipping_address" rows="4" class="form-control" required placeholder="Nama penerima, alamat lengkap, kode pos, no. HP">{{ old('shipping_address') }}</textarea>
                <p class="small text-muted mt-2">Pesanan dari beberapa toko akan dipecah menjadi beberapa order. Pembayaran dilakukan melalui QRIS untuk setiap pesanan.</p>
                <button class="btn btn-primary">Buat Pesanan</button>
            </form>
        </div></div>
    </div>
    <div class="col-md-5">
        <div class="card shadow-sm"><div class="card-body">
            <h6>Ringkasan</h6>
            @foreach($items as $i)
                <div class="d-flex justify-content-between small border-bottom py-1"><span>{{ $i->product->name }} x{{ $i->qty }}</span><span>Rp {{ number_format($i->qty * $i->product->price, 0, ',', '.') }}</span></div>
            @endforeach
            <div class="d-flex justify-content-between fw-bold mt-2"><span>Total</span><span>Rp {{ number_format($total, 0, ',', '.') }}</span></div>
        </div></div>
    </div>
</div>
@endsection
