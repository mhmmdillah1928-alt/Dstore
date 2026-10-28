@extends('layouts.app')
@section('title', 'Sengketa')
@section('content')
<h4 class="mb-3">Resolusi Sengketa / Retur</h4>
<div class="card shadow-sm"><div class="table-responsive">
<table class="table align-middle mb-0">
    <thead><tr><th>Order</th><th>Pembeli</th><th>Toko</th><th>Alasan</th><th>Status</th><th></th></tr></thead>
    @forelse($disputes as $d)
    <tr>
        <td>{{ $d->order->order_number }}</td><td>{{ $d->user->name }}</td><td>{{ $d->order->store->store_name }}</td>
        <td class="small">{{ \Illuminate\Support\Str::limit($d->reason, 60) }}</td>
        <td><span class="badge bg-{{ ['open' => 'warning text-dark', 'refunded' => 'success', 'rejected' => 'secondary'][$d->status] }}">{{ $d->status }}</span></td>
        <td><a href="{{ route('admin.disputes.show', $d) }}" class="btn btn-sm btn-outline-primary">Tinjau</a></td>
    </tr>
    @empty
    <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada sengketa.</td></tr>
    @endforelse
</table></div></div>
<div class="mt-3">{{ $disputes->links('pagination::bootstrap-5') }}</div>
@endsection
