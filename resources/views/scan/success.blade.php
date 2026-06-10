@extends('layouts.app')

@section('title', 'Scan Berhasil')

@push('styles')
<style>
    .scan-success-container {
        min-height: calc(100vh - 120px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        box-sizing: border-box;
    }
    
    .scan-card {
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(20px);
        border-radius: 24px;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        max-width: 100%;
        width: 480px;
        animation: slideUp 0.5s ease-out;
        box-sizing: border-box;
    }
    
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .scan-icon-wrapper {
        width: 120px;
        height: 120px;
        margin: 0 auto 1.5rem;
        background: linear-gradient(135deg, #34d399, #10b981);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
        animation: scaleIn 0.6s ease-out 0.2s both;
    }
    
    @keyframes scaleIn {
        from {
            transform: scale(0);
        }
        to {
            transform: scale(1);
        }
    }
    
    .scan-icon-wrapper i {
        font-size: 60px;
        color: white;
    }
    
    .scan-body {
        padding: 2rem;
        box-sizing: border-box;
    }
    
    .scan-title {
        color: #10b981;
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
    }
    
    .scan-details {
        background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
        border-radius: 16px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        text-align: left;
        box-sizing: border-box;
    }
    
    .scan-details p {
        margin-bottom: 0.75rem;
        font-size: 0.95rem;
    }
    
    .scan-details p:last-child {
        margin-bottom: 0;
    }
    
    .scan-details strong {
        color: #0f172a;
    }
    
    @media (max-width: 575.98px) {
        .scan-success-container {
            padding: 0.75rem;
            align-items: flex-start;
            padding-top: 2rem;
        }
        
        .scan-card {
            border-radius: 20px;
        }
        
        .scan-icon-wrapper {
            width: 100px;
            height: 100px;
            margin-bottom: 1.25rem;
        }
        
        .scan-icon-wrapper i {
            font-size: 50px;
        }
        
        .scan-body {
            padding: 1.5rem;
        }
        
        .scan-title {
            font-size: 1.5rem;
            margin-bottom: 1.25rem;
        }
        
        .scan-details {
            padding: 1rem;
            border-radius: 12px;
        }
        
        .scan-details p {
            font-size: 0.85rem;
        }
    }
</style>
@endpush

@section('content')
<div class="scan-success-container">
    <div class="scan-card">
        <div class="scan-body text-center">
            <div class="scan-icon-wrapper">
                <i class="fas fa-check-circle"></i>
            </div>
            
            <h3 class="scan-title">{{ $message }}</h3>
            
            <div class="scan-details">
                <p><strong>Event:</strong> {{ $event->nama_event }}</p>
                <p><strong>Sesi:</strong> {{ $session->nama_sesi }}</p>
                <p><strong>Waktu:</strong> {{ date('H:i:s') }} WIB</p>
            </div>
            
            <a href="{{ route('user.dashboard') }}" class="btn btn-primary w-100 py-3" style="border-radius: 14px; font-weight: 600;">
                <i class="fas fa-home me-2"></i>Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
