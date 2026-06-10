@extends('layouts.app')

@section('title', 'Laporan Kehadiran - Daurah Syariyyah')

@section('content')
<style>
@media (max-width: 575.98px) {
    .history-page .card { margin-bottom: 1rem; }
    .history-page .form-label { font-size: 0.875rem; }
    .history-page .form-control, .history-page .form-select { font-size: 0.875rem; padding: 0.5rem 0.75rem; }
    .history-page .btn { width: 100%; margin-bottom: 0.5rem; }
    .history-page h2 { font-size: 1.25rem; }
    .history-page .table th, .history-page .table td { font-size: 0.75rem; padding: 0.5rem; white-space: nowrap; }
}
</style>
<div class="container mt-4 mb-5 history-page">
    <h2 class="mb-4"><i class="fas fa-file-alt me-2"></i>Laporan Kehadiran</h2>
    
    <div class="card mb-4">
        <div class="card-header bg-white fw-bold">
            <i class="fa fa-filter me-2"></i>Filter Data
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.history') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Nama Event</label>
                        <select name="event_id" class="form-select">
                            <option value="">-- Semua Event --</option>
                            @foreach($events as $e)
                            <option value="{{ $e->id }}" {{ $eventId == $e->id ? 'selected' : '' }}>
                                {{ $e->nama_event }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-2 d-grid gap-2">
                        <button type="submit" class="btn btn btn-primary gradient-bg">
                            <i class="fa fa-search"></i> Tampilkan
                        </button>
                        <a href="{{ route('admin.history') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body p-0">
            @if($attendances->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle w-100" id="historyTable">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Waktu Scan</th>
                            <th>Nama Peserta</th>
                            <th>Lembaga</th>
                            <th>Event & Sesi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendances as $index => $att)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold">{{ date('H:i', strtotime($att->waktu_scan)) }}</div>
                                <small class="text-muted">{{ date('d/m/Y', strtotime($att->waktu_scan)) }}</small>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $att->user->nama }}</div>
                                <small class="text-muted"><i class="fa fa-phone me-1"></i>+{{ $att->user->nohp }}</small>
                            </td>
                            <td>{{ $att->user->lembaga }}</td>
                            <td>
                                <div class="gradient-text fw-bold">{{ $att->event->nama_event }}</div>
                                <span class="badge bg-success rounded-pill">{{ $att->session->nama_sesi ?? 'Sesi' }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5 text-muted">
                <i class="fas fa-file-times fa-3x mb-3"></i>
                <p>Belum ada data kehadiran</p>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#historyTable').DataTable({
            order: [[ 1, "desc" ]],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json"
            }
        });
    });
</script>
@endpush
@endsection
