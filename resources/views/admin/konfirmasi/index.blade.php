@extends('layouts.app')

@section('title', 'Konfirmasi Peserta - Daurah Syariyyah')

@section('content')
<div class="container">
    <h2 class="mb-3"><i class="fas fa-check-circle me-2"></i>Konfirmasi Peserta</h2>
    
    @if($event)
    <div class="d-flex gap-2 mb-3 flex-wrap">
        <a href="{{ route('admin.konfirmasi.export', $event->id) }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-download me-1"></i><span class="d-none d-sm-inline">Export CSV</span>
        </a>
        <form action="{{ route('admin.konfirmasi.acc-all', $event->id) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-success btn-sm">
                <i class="fas fa-check-double me-1"></i><span class="d-none d-sm-inline">ACC Semua</span>
            </button>
        </form>
    </div>
    @endif
    
<div class="card mb-3">
        <div class="card-body p-3">
            <form action="{{ route('admin.konfirmasi.index') }}" method="GET">
                <select name="event_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Event --</option>
                    @foreach($events as $e)
                    <option value="{{ $e->id }}" {{ $event && $event->id == $e->id ? 'selected' : '' }}>
                        {{ $e->nama_event }} ({{ date('d M Y', strtotime($e->tanggal)) }})
                    </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>
    
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    
    @if($event)
    <div class="alert alert-info">
        <strong>{{ $event->nama_event }}</strong> - {{ date('d F Y', strtotime($event->tanggal)) }}
        <br>
        <small>
            Total Pendaftar: {{ $registrations->count() }} | 
            Terkonfirmasi: {{ $registrations->where('status', 'confirmed')->count() }} | 
            Pending: {{ $registrations->where('status', 'pending')->count() }}
        </small>
    </div>
    @endif
    
    <div class="card">
        <div class="card-body p-0">
            @if($registrations->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Lembaga</th>
                            <th>Domisili</th>
                            <th>No WA</th>
                            <th>Menginap</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($registrations as $index => $reg)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $reg->user->nama }}</strong>
                                @if($reg->event)
                                <br><small class="text-muted">{{ $reg->event->nama_event }}</small>
                                @endif
                            </td>
                            <td>{{ $reg->user->lembaga }}</td>
                            <td>{{ $reg->user->domisili }}</td>
                            <td>
                                <a href="https://wa.me/62{{ substr($reg->user->nohp, 1) }}" target="_blank" class="text-success">
                                    {{ $reg->user->nohp }}
                                </a>
                            </td>
                            <td>
                                @if($reg->user->menginap == 'ya')
                                <span class="badge gradient-bg">Ya</span>
                                @else
                                <span class="badge bg-secondary">Tidak</span>
                                @endif
                            </td>
                            <td>
                                @if($reg->status == 'confirmed')
                                <span class="badge bg-success">Terkonfirmasi</span>
                                @else
                                <span class="badge bg-warning">Pending</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    @if($reg->status != 'confirmed')
                                    <form action="{{ route('admin.konfirmasi.acc', $reg->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success" title="Konfirmasi">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    @endif
                                    
                                    @if($reg->status == 'confirmed')
                                    <a href="{{ route('admin.konfirmasi.wa', $reg->id) }}" target="_blank" class="btn btn-success" title="Kirim WA">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                    @endif
                                    
                                    <form action="{{ route('admin.konfirmasi.hapus', $reg->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pendaftaran ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5 text-muted">
                <i class="fas fa-users-times fa-3x mb-3"></i>
                <p>Belum ada pendaftar{{ $event ? ' untuk event ini' : '' }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
