@extends('layouts.app')

@section('title', 'Scan Gagal')

@section('content')
<div class="container py-4 py-md-5" style="box-sizing: border-box;">
    <div class="card shadow mx-auto mb-4" style="max-width: 100%; box-sizing: border-box;">
        <div class="card-body text-center py-4 py-md-5 px-3 px-md-4" style="box-sizing: border-box;">
            <i class="fas fa-times-circle text-danger fa-4x fa-md-5x mb-3 mb-md-4"></i>
            <h3 class="text-danger mb-3 fs-5 fs-md-4">Gagal Absen</h3>
            <p class="text-muted mb-4 px-2">{{ $message }}</p>
            
            <a href="{{ route('user.dashboard') }}" class="btn btn btn-primary gradient-bg mt-2 mt-md-4 w-100 w-md-auto">
                <i class="fas fa-home me-2"></i>Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
