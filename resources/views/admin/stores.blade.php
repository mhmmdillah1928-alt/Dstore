@extends('layouts.app')
@section('title', 'Verifikasi Toko')
@section('content')
<h4 class="mb-3">Verifikasi Toko</h4>
<div class="card shadow-sm"><div class="table-responsive">
<table class="table align-middle mb-0">
    <thead><tr><th>Toko</th><th>Pemilik</th><th>Alamat</th><th>Status</th><th></th></tr></thead>
    @foreach($stores as $s)
    <tr>
        <td><img src="{{ $s->logo_url }}" width="36" class="rounded-circle me-1"> {{ $s->store_name }}</td>
        <td>{{ $s->user->name }}<div class="small text-muted">{{ $s->user->email }}</div></td>
        <td class="small">{{ $s->address }}</td>
        <td>@if($s->is_verified)<span class="badge bg-success">Terverifikasi</span>@else<span class="badge bg-warning text-dark">Menunggu</span>@endif</td>
        <td><form method="POST" action="{{ route('admin.stores.toggle', $s) }}">@csrf
            <button class="btn btn-sm {{ $s->is_verified ? 'btn-outline-danger' : 'btn-success' }}">{{ $s->is_verified ? 'Cabut' : 'Verifikasi' }}</button></form></td>
    </tr>
    @endforeach
</table></div></div>
<div class="mt-3">{{ $stores->links('pagination::bootstrap-5') }}</div>
@endsection
