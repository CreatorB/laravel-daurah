@extends('layouts.app')

@section('title', 'Admin Dashboard - Daurah Syariyyah')

@section('content')
<div class="container">
    <h2 class="mb-3 gradient-text"><i class="fas fa-tachometer-alt me-2"></i>Dashboard Admin</h2>
    
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.events.index') }}" class="text-decoration-none">
                <div class="card text-center h-100 gradient-bg text-white" style="cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.03)'" onmouseout="this.style.transform='scale(1)'">
                    <div class="card-body p-2">
                        <i class="fas fa-calendar-alt fa-2x mb-2 opacity-75"></i>
                        <h3 class="mb-1">{{ $stats['total_events'] }}</h3>
                        <small class="opacity-75" style="font-size: 0.7rem;">Total Event</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.users.index') }}" class="text-decoration-none">
                <div class="card text-center h-100" style="background: linear-gradient(135deg, #10b981, #34d399); color: white; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.03)'" onmouseout="this.style.transform='scale(1)'">
                    <div class="card-body p-2">
                        <i class="fas fa-users fa-2x mb-2 opacity-75"></i>
                        <h3 class="mb-1">{{ $stats['total_users'] }}</h3>
                        <small class="opacity-75" style="font-size: 0.7rem;">Total User</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center h-100" style="background: linear-gradient(135deg, #f59e0b, #fbbf24); color: white;">
                <div class="card-body p-2">
                    <i class="fas fa-clock fa-2x mb-2 opacity-75"></i>
                    <h3 class="mb-1">{{ $stats['pending_confirmations'] }}</h3>
                    <small class="opacity-75" style="font-size: 0.7rem;">Pending</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center h-100" style="background: linear-gradient(135deg, #6366f1, #818cf8); color: white;">
                <div class="card-body p-2">
                    <i class="fas fa-check-circle fa-2x mb-2 opacity-75"></i>
                    <h3 class="mb-1">{{ $stats['total_attendances'] }}</h3>
                    <small class="opacity-75" style="font-size: 0.7rem;">Kehadiran</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-4">
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header gradient-bg">
                    <i class="fas fa-calendar me-2"></i>Event Terbaru
                </div>
                <div class="card-body p-0">
                    @if($recentEvents->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Event</th>
                                    <th>Tanggal</th>
                                    <th>Sesi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentEvents as $event)
                                <tr>
                                    <td>{{ $event->nama_event }}</td>
                                    <td>{{ date('d M Y', strtotime($event->tanggal)) }}</td>
                                    <td>{{ $event->sessions->count() }}</td>
                                    <td>
                                        <a href="{{ route('monitor.index', $event->id) }}" target="_blank" class="btn btn-sm btn-warning" title="Monitor QR">
                                            <i class="fas fa-tv"></i>
                                        </a>
                                        <a href="{{ route('download.qr', $event->id) }}" class="btn btn-sm btn-success" title="Download QR">
                                            <i class="fas fa-qrcode"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4 text-muted">Belum ada event</div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header gradient-bg">
                    <i class="fas fa-user-clock me-2"></i>Menunggu Konfirmasi
                </div>
                <div class="card-body p-0">
                    @if($pendingRegistrations->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Event</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingRegistrations as $reg)
                                <tr>
                                    <td>{{ $reg->user->nama }}</td>
                                    <td>{{ $reg->event->nama_event }}</td>
                                    <td>
                                        <form action="{{ route('admin.konfirmasi.acc', $reg->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Konfirmasi">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4 text-muted">Tidak ada yang menunggu</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header gradient-bg">
                    <i class="fas fa-link me-2"></i>Quick Links
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6 col-md-3">
                            <a href="{{ route('admin.events.create') }}" class="btn btn-primary w-100" style="font-size: 0.8rem; padding: 0.5rem 0.75rem;">
                                <i class="fas fa-plus me-1"></i><span class="d-none d-md-inline">Buat</span> Event
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="{{ route('admin.konfirmasi.index') }}" class="btn btn-success w-100" style="font-size: 0.8rem; padding: 0.5rem 0.75rem;">
                                <i class="fas fa-check-circle me-1"></i><span class="d-none d-md-inline">Konfirmasi</span>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-info w-100 text-white" style="font-size: 0.8rem; padding: 0.5rem 0.75rem;">
                                <i class="fas fa-users me-1"></i><span class="d-none d-md-inline">Kelola</span> User
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="{{ route('admin.history') }}" class="btn btn-warning w-100 text-white" style="font-size: 0.8rem; padding: 0.5rem 0.75rem;">
                                <i class="fas fa-file-alt me-1"></i><span class="d-none d-md-inline">Laporan</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
