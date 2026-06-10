@extends('layouts.app')

@section('title', 'Kelola Event - Daurah Syariyyah')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h2><i class="fas fa-calendar me-2"></i>Kelola Event</h2>
        <a href="{{ route('admin.events.create') }}" class="btn btn btn-primary gradient-bg">
            <i class="fas fa-plus me-1"></i><span class="d-none d-sm-inline">Event</span> Baru
        </a>
    </div>
    
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
<div class="card">
        <div class="card-body p-0">
            @if($events->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Tanggal</th>
                            <th>Sesi</th>
                            <th>Konfirmasi</th>
                            <th>Radius</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($events as $event)
                        <tr>
                            <td>
                                <strong>{{ $event->nama_event }}</strong>
                                @if($event->auto_confirm)
                                <span class="badge bg-success ms-1">Auto</span>
                                @endif
                            </td>
                            <td class="d-none d-md-table-cell">{{ date('d M Y', strtotime($event->tanggal)) }}</td>
                            <td class="text-center">{{ $event->sessions->count() }}</td>
                            <td>
                               <a href="{{ route('admin.konfirmasi.event', $event->id) }}" class="btn btn-sm btn-outline-success">
                                    <span class="d-none d-md-inline">{{ $event->registrations->where('status', 'confirmed')->count() }}/{{ $event->registrations->count() }}</span>
                                    <span class="d-md-none">{{ $event->registrations->where('status', 'confirmed')->count() }}</span>
                                </a>
                            </td>
                            <td class="d-none d-lg-table-cell">
                                @if($event->radius_active)
                                <span class="badge bg-info">{{ $event->radius_meters }}m</span>
                                @else
                                <span class="badge bg-secondary">OFF</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.events.edit', $event->id) }}" class="btn btn-sm btn btn-primary gradient-bg" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus event ini?')">
                                        @csrf
                                        @method('POST')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
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
                <i class="fas fa-calendar-times fa-3x mb-3"></i>
                <p>Belum ada event. <a href="{{ route('admin.events.create') }}">Buat event baru</a></p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
