@extends('layouts.app')
@section('title', 'Penarikan Dana')
@section('content')
<h4 class="mb-3">Pengajuan Penarikan Dana</h4>
<div class="card shadow-sm"><div class="table-responsive">
<table class="table align-middle mb-0">
    <thead><tr><th>Tanggal</th><th>Toko</th><th>Jumlah</th><th>Status</th><th></th></tr></thead>
    @foreach($withdrawals as $w)
    <tr>
        <td>{{ $w->created_at->format('d M Y H:i') }}</td><td>{{ $w->store->store_name }}</td><td>Rp {{ number_format($w->amount, 0, ',', '.') }}</td>
        <td><span class="badge bg-{{ ['pending' => 'warning text-dark', 'approved' => 'success', 'rejected' => 'danger'][$w->status] }}">{{ $w->status }}</span></td>
        <td>@if($w->status === 'pending')
            <form method="POST" action="{{ route('admin.withdrawals.resolve', $w) }}" class="d-flex gap-1">@csrf
                <button name="action" value="approve" class="btn btn-sm btn-success">Setujui</button>
                <button name="action" value="reject" class="btn btn-sm btn-outline-danger">Tolak</button></form>
        @endif</td>
    </tr>
    @endforeach
</table></div></div>
<div class="mt-3">{{ $withdrawals->links('pagination::bootstrap-5') }}</div>
@endsection
