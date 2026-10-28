@extends('layouts.app')
@section('title', $product->exists ? 'Edit Produk' : 'Tambah Produk')
@section('content')
@php $v = fn($k) => old($k, $product->$k); @endphp
<div class="card shadow-sm"><div class="card-body">
    <h5>{{ $product->exists ? 'Edit' : 'Tambah' }} Produk</h5>
    <form method="POST" enctype="multipart/form-data"
          action="{{ $product->exists ? route('seller.products.update', $product) : route('seller.products.store') }}">
        @csrf @if($product->exists) @method('PUT') @endif
        <div class="row">
            <div class="col-md-8 mb-3"><label class="form-label">Judul Produk</label><input name="name" value="{{ $v('name') }}" class="form-control" required></div>
            <div class="col-md-4 mb-3"><label class="form-label">Brand</label>
                <select name="brand_id" class="form-select" required>@foreach($brands as $b)<option value="{{ $b->id }}" @selected($v('brand_id') == $b->id)>{{ $b->name }}</option>@endforeach</select></div>
            <div class="col-md-3 mb-3"><label class="form-label">Kondisi</label>
                <select name="condition" class="form-select"><option value="new" @selected($v('condition') === 'new')>Baru</option><option value="used" @selected($v('condition') === 'used')>Bekas</option></select></div>
            <div class="col-md-3 mb-3"><label class="form-label">Harga (Rp)</label><input type="number" name="price" value="{{ $v('price') !== null ? (int) $v('price') : '' }}" class="form-control" required></div>
            <div class="col-md-2 mb-3"><label class="form-label">Stok</label><input type="number" name="stock" value="{{ $v('stock') ?? 1 }}" class="form-control" required></div>
            <div class="col-md-2 mb-3"><label class="form-label">RAM (GB)</label><input type="number" name="ram" value="{{ $v('ram') }}" class="form-control" required></div>
            <div class="col-md-2 mb-3"><label class="form-label">Storage (GB)</label><input type="number" name="storage" value="{{ $v('storage') }}" class="form-control" required></div>
            <div class="col-md-3 mb-3"><label class="form-label">Berat (gram)</label><input type="number" name="weight" value="{{ $v('weight') ?? 200 }}" class="form-control" required></div>
        </div>
        <div class="mb-3"><label class="form-label">Deskripsi (termasuk kondisi fisik)</label><textarea name="description" rows="5" class="form-control">{{ $v('description') }}</textarea></div>
        <div class="mb-3"><label class="form-label">Foto Produk (bisa lebih dari satu, maks 2MB)</label><input type="file" name="images[]" multiple accept="image/*" class="form-control"></div>
        @if($product->exists && $product->images->count())
            <div class="d-flex gap-2 flex-wrap mb-3">
                @foreach($product->images as $img)
                    <div class="position-relative"><img src="{{ asset('storage/'.$img->image_path) }}" width="90" class="rounded border">
                        <button type="submit" form="del{{ $img->id }}" class="btn btn-danger btn-sm position-absolute top-0 end-0 py-0 px-1">&times;</button></div>
                @endforeach
            </div>
        @endif
        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('seller.products.index') }}" class="btn btn-link">Batal</a>
    </form>
    @if($product->exists)
        @foreach($product->images as $img)
            <form id="del{{ $img->id }}" method="POST" action="{{ route('seller.products.image.destroy', $img) }}" onsubmit="return confirm('Hapus foto?')">@csrf @method('DELETE')</form>
        @endforeach
    @endif
</div></div>
@endsection
