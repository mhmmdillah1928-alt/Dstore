@extends('layouts.app')
@section('title', 'Saldo & Penarikan')
@section('content')
<div class="row g-3">
    <div class="col-md-4">
        <div class="card shadow-sm mb-3"><div class="card-body"><div class="text-muted small">Saldo Tersedia</div><div class="fs-3 fw-bold text-success">Rp {{ number_format($store->balance, 0, ',', '.') }}</div></div></div>
        <form method="POST" action="{{ route('seller.withdrawals.store') }}" class="card card-body shadow-sm">
            @csrf <h6>Ajukan Penarikan</h6>
            <input type="number" name="amount" min="10000" class="form-control mb-2" placeholder="Jumlah (min Rp 10.000)" required>
            <button class="btn btn-primary">Ajukan</button>
        </form>
    </div>
    <div class="col-md-8"><div class="card shadow-sm"><div class="table-responsive">
        <table class="table mb-0"><thead><tr><th>Tanggal</th><th>Jumlah</th><th>Status</th></tr></thead>
        @forelse($withdrawals as $w)
            <tr><td>{{ $w->created_at->format('d M Y H:i') }}</td><td>Rp {{ number_format($w->amount, 0, ',', '.') }}</td>
                <td><span class="badge bg-{{ ['pending' => 'warning text-dark', 'approved' => 'success', 'rejected' => 'danger'][$w->status] }}">{{ $w->status }}</span></td></tr>
        @empty
            <tr><td colspan="3" class="text-center text-muted py-3">Belum ada penarikan.</td></tr>
        @endforelse
        </table></div></div>
        <div class="mt-3">{{ $withdrawals->links('pagination::bootstrap-5') }}</div>
    </div>
</div>
@endsection
