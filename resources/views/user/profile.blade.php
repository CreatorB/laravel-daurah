@extends('layouts.app')

@section('title', 'Edit Profil - Daurah Syariyyah')

@section('content')
<div class="container">
    <h2 class="mb-3"><i class="fas fa-user-edit me-2"></i>Edit Profil</h2>
    
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
<div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">Data Diri</div>
                <div class="card-body p-3 p-md-4">
                    <form action="{{ route('user.profile.update') }}" method="POST">
                        @csrf
                        
<div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" style="font-size: 0.9rem;">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control" value="{{ old('nama', $user->nama) }}" required>
                            </div>
                            
                            <div class="col-12 col-md-6">
                                <label class="form-label" style="font-size: 0.9rem;">Lembaga</label>
                                <input type="text" name="lembaga" class="form-control" value="{{ old('lembaga', $user->lembaga) }}" placeholder="Nama lembaga atau PRIBADI">
                            </div>
                            
                            <div class="col-12 col-md-6">
                                <label class="form-label" style="font-size: 0.9rem;">Domisili <span class="text-danger">*</span></label>
                                <input type="text" name="domisili" class="form-control" value="{{ old('domisili', $user->domisili) }}" required>
                            </div>
                            
                            <div class="col-12 col-md-6">
                                <label class="form-label" style="font-size: 0.9rem;">Nomor WhatsApp <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">+62</span>
                                    <input type="text" name="nohp" class="form-control" value="{{ old('nohp', substr($user->nohp, 2)) }}" required>
                                </div>
                            </div>
                            
                            <div class="col-12 col-md-6">
                                <label class="form-label" style="font-size: 0.9rem;">Menginap? <span class="text-danger">*</span></label>
                                <select name="menginap" class="form-select" required>
                                    <option value="ya" {{ $user->menginap == 'ya' ? 'selected' : '' }}>Ya</option>
                                    <option value="tidak" {{ $user->menginap == 'tidak' ? 'selected' : '' }}>Tidak</option>
                                </select>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn btn-primary gradient-bg btn-lg w-100 mt-3">
                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
