@extends('layouts.app')
@section('title', 'Daftar')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm"><div class="card-body p-4">
            <h4 class="mb-3">Daftar Akun</h4>
            <form method="POST" action="{{ route('register') }}" x-data>
                @csrf
                <div class="mb-3">
                    <label class="form-label d-block">Daftar sebagai</label>
                    <div class="btn-group w-100">
                        <input type="radio" class="btn-check" name="role" id="rc" value="customer" {{ old('role', 'customer') === 'customer' ? 'checked' : '' }} onchange="toggleStore()">
                        <label class="btn btn-outline-primary" for="rc">Pembeli</label>
                        <input type="radio" class="btn-check" name="role" id="rs" value="seller" {{ old('role') === 'seller' ? 'checked' : '' }} onchange="toggleStore()">
                        <label class="btn btn-outline-primary" for="rs">Penjual</label>
                    </div>
                </div>
                <div class="mb-3"><label class="form-label">Nama</label><input name="name" value="{{ old('name') }}" class="form-control" required></div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label">Email</label><input type="email" name="email" value="{{ old('email') }}" class="form-control" required></div>
                    <div class="col-md-6 mb-3"><label class="form-label">No. HP</label><input name="phone" value="{{ old('phone') }}" class="form-control" required></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Konfirmasi</label><input type="password" name="password_confirmation" class="form-control" required></div>
                </div>
                <div id="storeFields" class="border rounded p-3 mb-3 bg-light" style="display:none">
                    <div class="mb-3"><label class="form-label">Nama Toko</label><input name="store_name" value="{{ old('store_name') }}" class="form-control"></div>
                    <div><label class="form-label">Alamat Pengiriman / Toko</label><textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea></div>
                    <div class="form-text">Toko perlu diverifikasi admin sebelum dapat berjualan.</div>
                </div>
                <button class="btn btn-primary w-100">Daftar</button>
            </form>
        </div></div>
    </div>
</div>
<script>
function toggleStore() { document.getElementById('storeFields').style.display = document.getElementById('rs').checked ? 'block' : 'none'; }
toggleStore();
</script>
@endsection
