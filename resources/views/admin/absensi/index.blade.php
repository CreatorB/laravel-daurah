@extends('layouts.app')

@section('title', 'Log Absensi - Daurah Syariyyah')

@section('content')
<div class="container">
    <h2 class="mb-3"><i class="fas fa-clipboard-list me-2"></i>Log Absensi</h2>

    @if($event)
    <div class="d-flex gap-2 mb-3 flex-wrap">
        <a href="{{ route('admin.absensi.export-csv', $event->id) }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-file-csv me-1"></i><span class="d-none d-sm-inline">Export CSV</span>
        </a>
        <a href="{{ route('admin.absensi.export-excel', $event->id) }}" class="btn btn-outline-success btn-sm">
            <i class="fas fa-file-excel me-1"></i><span class="d-none d-sm-inline">Export Excel</span>
        </a>
    </div>
    @endif

    <div class="card mb-3">
        <div class="card-body p-3">
            <form action="{{ route('admin.absensi.index') }}" method="GET">
                <select name="event_id" class="form-select form-select-sm" onchange="if(this.value){ window.location = '{{ url('admin/absensi') }}/' + this.value; } else { window.location = '{{ route('admin.absensi.index') }}'; }">
                    <option value="">-- Pilih Event --</option>
                    @foreach($events as $e)
                    <option value="{{ $e->id }}" {{ $event && $event->id == $e->id ? 'selected' : '' }}>
                        {{ $e->nama_event }} ({{ date('d M Y', strtotime($e->tanggal)) }})
                    </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    @if($event)
    <div class="alert alert-info">
        <strong>{{ $event->nama_event }}</strong> - {{ date('d F Y', strtotime($event->tanggal)) }}
    </div>

    @forelse($sessions as $sessionRow)
    <div class="card mb-3">
        <div class="card-header gradient-bg d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span><i class="fas fa-broadcast-tower me-2"></i>{{ $sessionRow['nama_sesi'] }} ({{ $sessionRow['jam_range'] }})</span>
            <span class="badge bg-light text-dark">{{ count($sessionRow['attendances']) }} hadir</span>
        </div>
        <div class="card-body p-0">
            @if(count($sessionRow['attendances']) > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Lembaga</th>
                            <th>Domisili</th>
                            <th>Waktu Absen</th>
                            <th>Konfirmasi Materi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sessionRow['attendances'] as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $row['nama'] }}</strong></td>
                            <td>{{ $row['lembaga'] }}</td>
                            <td>{{ $row['domisili'] }}</td>
                            <td>{{ $row['waktu_scan_readable'] }}</td>
                            <td>
                                @if($row['materi_confirmed'])
                                <span class="badge bg-success">Sudah</span>
                                @else
                                <span class="badge bg-warning">Belum</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-4 text-muted">
                <i class="fas fa-user-slash mb-2"></i>
                <p class="mb-0">Belum ada yang absen untuk sesi ini</p>
            </div>
            @endif
        </div>
    </div>
    @empty
    <div class="text-center py-5 text-muted">
        <i class="fas fa-calendar-times fa-3x mb-3"></i>
        <p>Event ini belum punya sesi</p>
    </div>
    @endforelse
    @else
    <div class="text-center py-5 text-muted">
        <i class="fas fa-hand-pointer fa-3x mb-3"></i>
        <p>Pilih event di atas untuk melihat log absensi</p>
    </div>
    @endif
</div>
@endsection
