@extends('layouts.app')

@section('title', 'Maintenance - Daurah Syariyyah')

@section('content')
<div class="container">
    <h2 class="mb-3 gradient-text"><i class="fas fa-tools me-2"></i>Maintenance</h2>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header gradient-bg">
                    <i class="fas fa-broom me-2"></i>Cache Management
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.maintenance.clear-cache') }}" class="btn btn-primary">
                            <i class="fas fa-sync me-2"></i>Clear Cache
                        </a>
                        <a href="{{ route('admin.maintenance.clear-view') }}" class="btn btn-primary">
                            <i class="fas fa-eye-slash me-2"></i>Clear View Cache
                        </a>
                        <a href="{{ route('admin.maintenance.optimize') }}" class="btn btn-primary">
                            <i class="fas fa-rocket me-2"></i>Optimize Application
                        </a>
                        <a href="{{ route('admin.maintenance.clear-all') }}" class="btn btn-primary">
                            <i class="fas fa-trash-alt me-2"></i>Clear All Caches
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header gradient-bg">
                    <i class="fas fa-database me-2"></i>Database Management
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.maintenance.migrate') }}" class="btn btn-success">
                            <i class="fas fa-play me-2"></i>Run Migrations
                        </a>
                        <a href="{{ route('admin.maintenance.db-status') }}" class="btn btn-success">
                            <i class="fas fa-table me-2"></i>Database Status
                        </a>
                        <a href="{{ route('admin.maintenance.storage-link') }}" class="btn btn-success">
                            <i class="fas fa-link me-2"></i>Storage Link
                        </a>
                        <form action="{{ route('admin.maintenance.seed-admin') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-user-plus me-2"></i>Seed Admin (089619060672)
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <i class="fas fa-exclamation-triangle me-2"></i>Danger Zone
                </div>
                <div class="card-body">
                    <h5 class="card-title">Reset Database</h5>
                    <p class="card-text text-muted">This will drop all tables, re-run migrations, seed admin account (089619060672), and delete all uploaded files.</p>
                    <form action="{{ route('admin.maintenance.reset-db') }}" method="POST" onsubmit="return confirm('PERHATIAN: Semua data akan dihapus permanen! Lanjutkan?')">
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash-alt me-2"></i>Reset Database
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
