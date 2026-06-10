@extends('layouts.app')

@section('title', 'Edit User - Daurah Syariyyah')

@section('content')
<div class="container py-3 py-md-4">
    <h2 class="mb-3 mb-md-4 fs-4 fs-md-3"><i class="fas fa-user-edit me-2"></i>Edit User</h2>
    
    <div class="row justify-content-center">
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header">Data User</div>
                <div class="card-body">
                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('POST')
                        <div class="mb-3">
                            <label class="form-label">Nama<span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" value="{{ old('nama', $user->nama) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No HP<span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">+62</span>
                                <input type="text" name="nohp" class="form-control" value="{{ old('nohp', substr($user->nohp, 2)) }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lembaga</label>
                            <input type="text" name="lembaga" class="form-control" value="{{ old('lembaga', $user->lembaga) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="3">{{ old('alamat', $user->alamat) }}</textarea>
                        </div>
                        <button type="submit" class="btn btn btn-primary gradient-bg w-100">
                            <i class="fas fa-save me-2"></i>Update
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
