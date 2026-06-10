@extends('layouts.app')

@section('title', 'Buat Event Baru - Daurah Syariyyah')

@section('content')
<div class="container py-3 py-md-4">
    <h2 class="mb-3 mb-md-4 fs-4 fs-md-3"><i class="fas fa-plus-circle me-2"></i>Buat Event Baru</h2>
    
    @if($errors->any())
    <div class="alert alert-danger mb-3 mb-md-4">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    
    <form action="{{ route('admin.events.store') }}" method="POST">
        @csrf
        
        <div class="card mb-3 mb-md-4">
            <div class="card-header">Informasi Event</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Nama Event<span class="text-danger">*</span></label>
                        <input type="text" name="nama_event" class="form-control" value="{{ old('nama_event') }}" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal') }}" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">QR Mode <span class="text-danger">*</span></label>
                        <select name="qr_mode" class="form-select" required>
                            <option value="static" {{ old('qr_mode') == 'static' ? 'selected' : '' }}>Static (Tetap)</option>
                            <option value="dynamic" {{ old('qr_mode') == 'dynamic' ? 'selected' : '' }}>Dynamic (Berubah)</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Group Link</label>
                        <input type="url" name="group_link" class="form-control" value="{{ old('group_link') }}" placeholder="https://chat.whatsapp.com/...">
                        <small class="text-muted">Link grup WhatsApp untuk komunikasi peserta</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mb-3 mb-md-4">
            <div class="card-header">Pengaturan Kehadiran</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Radius Absen</label>
                        <div class="input-group">
                            <input type="number" name="radius_meters" id="radius_meters_c" class="form-control" value="{{ old('radius_meters', 100) }}" min="1" oninput="updateDiameter('c')">
                            <span class="input-group-text">meter</span>
                        </div>
                        <small class="text-muted">Diameter: <span id="diameter_c" class="fw-semibold">200</span> m &nbsp;|&nbsp; sekitar <span id="diameter_km_c" class="fw-semibold">0.2</span> km</small>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Koordinat Lokasi (Lat, Lng)</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="number" step="0.00000001" name="radius_lat" class="form-control" value="{{ old('radius_lat') }}" placeholder="Latitude">
                            </div>
                            <div class="col-6">
                                <input type="number" step="0.00000001" name="radius_lng" class="form-control" value="{{ old('radius_lng') }}" placeholder="Longitude">
                            </div>
                        </div>
                        <small class="text-muted">Tips: klik kanan di Google Maps → "What's here?" untuk koordinat</small>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" name="radius_active" id="radius_active" class="form-check-input" value="1" {{ old('radius_active') ? 'checked' : '' }}>
                            <label class="form-check-label" for="radius_active">
                                Aktifkan Absen Berbasis Radius
                            </label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" name="auto_confirm" id="auto_confirm" class="form-check-input" value="1" {{ old('auto_confirm') ? 'checked' : '' }}>
                            <label class="form-check-label" for="auto_confirm">
                                Auto Konfirmasi (Pendaftar langsung terkonfirmasi saat daftar)
                            </label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" name="auto_invite" id="auto_invite" class="form-check-input" value="1" {{ old('auto_invite') ? 'checked' : '' }}>
                            <label class="form-check-label" for="auto_invite">
                                Auto Invite (Event muncul di dashboard semua peserta)
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mb-3 mb-md-4">
            <div class="card-header">Pengaturan Materi</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Tipe Konfirmasi Materi <span class="text-danger">*</span></label>
                        <select name="material_type" class="form-select" required>
                            <option value="none" {{ old('material_type') == 'none' ? 'selected' : '' }}>Tidak ada konfirmasi materi</option>
                            <option value="once" {{ old('material_type') == 'once' ? 'selected' : '' }}>Sekali (di awal event)</option>
                            <option value="per_session" {{ old('material_type') == 'per_session' ? 'selected' : '' }}>Per Sesi</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mb-3 mb-md-4">
            <div class="card-header">Sesi Event</div>
            <div class="card-body">
                <div id="sessions-wrapper">
                    <div class="session-row row g-3 mb-3">
                        <div class="col-12 col-md-4">
                            <input type="text" name="sessions[0][nama_sesi]" class="form-control" placeholder="Nama Sesi (cth: Sesi 1)" required>
                        </div>
                        <div class="col-12 col-md-3">
                            <input type="time" name="sessions[0][jam_mulai]" class="form-control" required>
                        </div>
                        <div class="col-12 col-md-3">
                            <input type="time" name="sessions[0][jam_selesai]" class="form-control" required>
                        </div>
                        <div class="col-12 col-md-2">
                            <button type="button" class="btn btn-danger w-100" onclick="removeSession(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-secondary w-100 w-md-auto" onclick="addSession()">
                    <i class="fas fa-plus me-2"></i>Tambah Sesi
                </button>
            </div>
        </div>
        
        <div class="d-flex flex-column flex-md-row gap-2 gap-md-3">
            <button type="submit" class="btn btn btn-primary gradient-bg btn-lg flex-fill">
                <i class="fas fa-save me-2"></i>Simpan Event
            </button>
            <a href="{{ route('admin.events.index') }}" class="btn btn-secondary btn-lg flex-fill flex-md-grow-0">
                Batal
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
function updateDiameter(suffix) {
    const r = parseFloat(document.getElementById('radius_meters_' + suffix).value) || 0;
    const d = r * 2;
    document.getElementById('diameter_' + suffix).textContent = d;
    document.getElementById('diameter_km_' + suffix).textContent = (d / 1000).toFixed(2);
}
updateDiameter('c');

let sessionCount = 1;

function addSession() {
    const wrapper = document.getElementById('sessions-wrapper');
    const html = `
        <div class="session-row row g-3 mb-3">
            <div class="col-12 col-md-4">
                <input type="text" name="sessions[${sessionCount}][nama_sesi]" class="form-control" placeholder="Nama Sesi" required>
            </div>
            <div class="col-12 col-md-3">
                <input type="time" name="sessions[${sessionCount}][jam_mulai]" class="form-control" required>
            </div>
            <div class="col-12 col-md-3">
                <input type="time" name="sessions[${sessionCount}][jam_selesai]" class="form-control" required>
            </div>
            <div class="col-12 col-md-2">
                <button type="button" class="btn btn-danger w-100" onclick="removeSession(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    wrapper.insertAdjacentHTML('beforeend', html);
    sessionCount++;
}

function removeSession(btn) {
    const rows = document.querySelectorAll('.session-row');
    if (rows.length > 1) {
        btn.closest('.session-row').remove();
    }
}
</script>
@endpush
@endsection
