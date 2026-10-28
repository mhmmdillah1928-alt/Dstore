@extends('layouts.app')
@section('title', 'Profil Toko')
@section('content')
<div class="row"><div class="col-md-7">
<div class="card shadow-sm"><div class="card-body">
    <h5>Profil Toko @if($store->is_verified)<span class="badge bg-success">Terverifikasi</span>@else<span class="badge bg-warning text-dark">Belum diverifikasi</span>@endif</h5>
    <form method="POST" action="{{ route('seller.store.update') }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <img src="{{ $store->logo_url }}" width="80" height="80" class="rounded-circle mb-2 object-fit-cover">
        <div class="mb-3"><label class="form-label">Logo</label><input type="file" name="logo" accept="image/*" class="form-control"></div>
        <div class="mb-3"><label class="form-label">Nama Toko</label><input name="store_name" value="{{ old('store_name', $store->store_name) }}" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Alamat Pengiriman</label><textarea name="address" rows="3" class="form-control" required>{{ old('address', $store->address) }}</textarea></div>
        <button class="btn btn-primary">Simpan</button>
    </form>
</div></div></div></div>
@endsection
