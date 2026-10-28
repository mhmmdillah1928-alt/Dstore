@extends('layouts.app')
@section('title', 'Produk Saya')
@section('content')
<div class="d-flex justify-content-between mb-3"><h4>Produk Saya</h4><a href="{{ route('seller.products.create') }}" class="btn btn-primary"><i class="bi bi-plus"></i> Tambah Produk</a></div>
<div class="card shadow-sm"><div class="table-responsive">
<table class="table align-middle mb-0">
    <thead><tr><th>Produk</th><th>Kondisi</th><th>Spesifikasi</th><th>Harga</th><th>Stok</th><th>Status</th><th></th></tr></thead>
    @forelse($products as $p)
    <tr>
        <td><div class="d-flex gap-2 align-items-center"><img src="{{ $p->thumbnail }}" width="48" class="rounded"><div>{{ $p->name }}<div class="small text-muted">{{ $p->brand->name }}</div></div></div></td>
        <td>{{ $p->condition_label }}</td>
        <td>{{ $p->ram }}GB / {{ $p->storage }}GB</td>
        <td>Rp {{ number_format($p->price, 0, ',', '.') }}</td>
        <td>{{ $p->stock }}</td>
        <td>@if($p->is_active)<span class="badge bg-success">Aktif</span>@else<span class="badge bg-secondary">Nonaktif</span>@endif</td>
        <td class="text-nowrap">
            <a href="{{ route('seller.products.edit', $p) }}" class="btn btn-sm btn-outline-primary">Edit</a>
            <form method="POST" action="{{ route('seller.products.destroy', $p) }}" class="d-inline" onsubmit="return confirm('Hapus produk?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus</button></form>
        </td>
    </tr>
    @empty
    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada produk.</td></tr>
    @endforelse
</table></div></div>
<div class="mt-3">{{ $products->links('pagination::bootstrap-5') }}</div>
@endsection
