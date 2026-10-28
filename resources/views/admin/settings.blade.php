@extends('layouts.app')
@section('title', 'Komisi')
@section('content')
<div class="row"><div class="col-md-5"><div class="card shadow-sm"><div class="card-body">
    <h5>Komisi Transaksi</h5>
    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf @method('PUT')
        <label class="form-label">Persentase komisi (%)</label>
        <input type="number" step="0.01" min="0" max="100" name="commission_percent" value="{{ old('commission_percent', $commission) }}" class="form-control mb-3" required>
        <button class="btn btn-primary">Simpan</button>
    </form>
</div></div></div></div>
@endsection
