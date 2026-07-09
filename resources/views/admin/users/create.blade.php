@extends('layouts.app')

@section('title', 'Tambah User - Daurah Syariyyah')

@section('content')
<div class="container py-3 py-md-4">
    <h2 class="mb-3 mb-md-4 fs-4 fs-md-3"><i class="fas fa-user-plus me-2"></i>Tambah User Baru</h2>
    
    <div class="row justify-content-center">
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header">Data User</div>
                <div class="card-body">
                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nama<span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No HP<span class="text-danger">*</span></label>
                            <input type="text" name="nohp" class="form-control @error('nohp') is-invalid @enderror" value="{{ old('nohp') }}" placeholder="081234567890" required>
                            <small class="text-muted">Contoh: 081234567890 (boleh dengan/tanpa +62 di depan)</small>
                            @error('nohp')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                            @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lembaga</label>
                            <input type="text" name="lembaga" class="form-control" value="{{ old('lembaga', 'PRIBADI') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat / Domisili</label>
                            <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3" placeholder="Alamat (juga akan tersimpan sebagai domisili)">{{ old('alamat') }}</textarea>
                            @error('alamat')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn btn-primary gradient-bg w-100">
                            <i class="fas fa-save me-2"></i>Simpan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
