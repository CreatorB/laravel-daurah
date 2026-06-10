@extends('layouts.app')

@section('title', 'Edit Event - Daurah Syariyyah')

@section('content')
<style>
@media (max-width: 575.98px) {
    .event-edit-page .card { margin-bottom: 1rem; }
    .event-edit-page .form-label { font-size: 0.875rem; }
    .event-edit-page .form-control, .event-edit-page .form-select { font-size: 0.875rem; padding: 0.5rem 0.75rem; }
    .event-edit-page .btn { width: 100%; margin-bottom: 0.5rem; }
    .event-edit-page .table th, .event-edit-page .table td { font-size: 0.75rem; padding: 0.5rem; }
    .event-edit-page h2 { font-size: 1.25rem; }
}
</style>
<div class="container event-edit-page">
    <h2 class="mb-4"><i class="fas fa-edit me-2"></i>Edit Event: {{ $event->nama_event }}</h2>
    
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
<form action="{{ route('admin.events.update', $event->id) }}" method="POST">
        @csrf
        @method('POST')
        
        <div class="card mb-4">
            <div class="card-header">Informasi Event</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Nama Event<span class="text-danger">*</span></label>
                        <input type="text" name="nama_event" class="form-control" value="{{ old('nama_event', $event->nama_event) }}" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', $event->tanggal) }}" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">QR Mode <span class="text-danger">*</span></label>
                        <select name="qr_mode" class="form-select" required>
                            <option value="static" {{ $event->qr_mode == 'static' ? 'selected' : '' }}>Static (Tetap)</option>
                            <option value="dynamic" {{ $event->qr_mode == 'dynamic' ? 'selected' : '' }}>Dynamic (Berubah)</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Group Link</label>
                        <input type="url" name="group_link" class="form-control" value="{{ old('group_link', $event->group_link) }}" placeholder="https://chat.whatsapp.com/...">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">Pengaturan Kehadiran</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Radius Absen</label>
                        <div class="input-group">
                            <input type="number" name="radius_meters" id="radius_meters_e" class="form-control" value="{{ old('radius_meters', $event->radius_meters) }}" min="1" oninput="updateDiameterEdit()">
                            <span class="input-group-text">meter</span>
                        </div>
                        <small class="text-muted">Diameter: <span id="diameter_e" class="fw-semibold">{{ ($event->radius_meters ?? 100) * 2 }}</span> m &nbsp;|&nbsp; sekitar <span id="diameter_km_e" class="fw-semibold">{{ number_format((($event->radius_meters ?? 100) * 2) / 1000, 2) }}</span> km</small>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Koordinat Lokasi (Lat, Lng)</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="number" step="0.00000001" name="radius_lat" class="form-control" value="{{ old('radius_lat', $event->radius_lat) }}" placeholder="Latitude">
                            </div>
                            <div class="col-6">
                                <input type="number" step="0.00000001" name="radius_lng" class="form-control" value="{{ old('radius_lng', $event->radius_lng) }}" placeholder="Longitude">
                            </div>
                        </div>
                        <small class="text-muted">Tips: klik kanan di Google Maps → "What's here?" untuk koordinat</small>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" name="radius_active" id="radius_active" class="form-check-input" value="1" {{ $event->radius_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="radius_active">Aktifkan Absen Berbasis Radius</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" name="auto_confirm" id="auto_confirm" class="form-check-input" value="1" {{ $event->auto_confirm ? 'checked' : '' }}>
                            <label class="form-check-label" for="auto_confirm">Auto Konfirmasi</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" name="auto_invite" id="auto_invite" class="form-check-input" value="1" {{ $event->auto_invite ? 'checked' : '' }}>
                            <label class="form-check-label" for="auto_invite">Auto Invite</label>
                        </div>
                        <small class="text-muted">Jika diaktifkan, event akan muncul di dashboard semua peserta (termasuk yang belum diundang)</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">Pengaturan Materi</div>
            <div class="card-body">
                <label class="form-label">Tipe Konfirmasi Materi<span class="text-danger">*</span></label>
                <select name="material_type" class="form-select" required>
                    <option value="none" {{ $event->material_type == 'none' ? 'selected' : '' }}>Tidak ada konfirmasi materi</option>
                    <option value="once" {{ $event->material_type == 'once' ? 'selected' : '' }}>Sekali (di awal event)</option>
                    <option value="per_session" {{ $event->material_type == 'per_session' ? 'selected' : '' }}>Per Sesi</option>
                </select>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary gradient-bg btn-lg w-100">
                <i class="fas fa-save me-2"></i>Update Event
            </button>
        </div>
    </form>

    <div class="card mt-4">
        <div class="card-header">Sesi Event</div>
        <div class="card-body">
            @if($event->sessions->count() > 0)
            <div class="table-responsive mb-3">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Sesi</th>
                            <th>Mulai</th>
                            <th>Selesai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($event->sessions as $session)
                        <tr>
                            <td>{{ $session->nama_sesi }}</td>
                            <td>{{ $session->jam_mulai }}</td>
                            <td>{{ $session->jam_selesai }}</td>
                            <td>
                                <form action="{{ route('admin.events.sessions.delete', $session->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus sesi ini?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
            
            <form action="{{ route('admin.events.sessions.store', $event->id) }}" method="POST" class="row g-2">
                @csrf
                <div class="col-12 col-md-4">
                    <input type="text" name="nama_sesi" class="form-control" placeholder="Nama Sesi" required>
                </div>
                <div class="col-12 col-md-3">
                    <input type="time" name="jam_mulai" class="form-control" required>
                </div>
                <div class="col-12 col-md-3">
                    <input type="time" name="jam_selesai" class="form-control" required>
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn btn-success w-100"><i class="fas fa-plus me-2"></i>Tambah</button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card mt-4">
        <div class="card-header">Undang Peserta</div>
        <div class="card-body">
            <form action="{{ route('admin.events.invite', $event->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Pilih User</label>
                    <select name="user_ids[]" class="form-select" multiple size="5">
                        @foreach($users as $user)
                        @if(!$registrations->contains('user_id', $user->id))
                        <option value="{{ $user->id }}">{{ $user->nama }} - {{ $user->nohp }}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn btn-primary gradient-bg">
                    <i class="fas fa-paper-plane me-2"></i>Undang Terpilih
                </button>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
function updateDiameterEdit() {
    const r = parseFloat(document.getElementById('radius_meters_e').value) || 0;
    const d = r * 2;
    document.getElementById('diameter_e').textContent = d;
    document.getElementById('diameter_km_e').textContent = (d / 1000).toFixed(2);
}
</script>
@endpush
@endsection
