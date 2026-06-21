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
    
    <form id="createEventForm" action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="card mb-3 mb-md-4">
            <div class="card-header">Informasi Event</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Nama Event<span class="text-danger">*</span></label>
                        <input type="text" name="nama_event" class="form-control" value="{{ old('nama_event') }}" required>
                        <div class="invalid-feedback">Nama event wajib diisi.</div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal') }}" required>
                        <div class="invalid-feedback">Tanggal wajib diisi.</div>
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
            <div class="card-header">Sertifikat</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="cert_enabled" id="cert_enabled_c" class="form-check-input" value="1" {{ old('cert_enabled') ? 'checked' : '' }} onchange="document.getElementById('cert-settings-c').classList.toggle('d-none', !this.checked)">
                            <label class="form-check-label" for="cert_enabled_c">Aktifkan Sertifikat</label>
                        </div>
                        <small class="text-muted">Jika diaktifkan, peserta yang hadir lengkap di semua sesi dapat generate sertifikat dari riwayat kehadirannya.</small>
                    </div>
                    <div id="cert-settings-c" class="row g-3 {{ old('cert_enabled') ? '' : 'd-none' }}">
                        <div class="col-12 col-md-6">
                            <label class="form-label">Template Sertifikat (gambar)</label>
                            <input type="file" name="cert_template" id="cert_template_c" class="form-control" accept="image/*">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Font Sertifikat (opsional)</label>
                            <input type="file" name="cert_font" class="form-control" accept=".ttf,.otf">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Ukuran Font</label>
                            <input type="number" name="cert_font_size" class="form-control" value="{{ old('cert_font_size', 30) }}">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Warna Font</label>
                            <input type="color" name="cert_font_color" class="form-control form-control-color" value="{{ old('cert_font_color', '#000000') }}">
                        </div>
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
                            <div class="invalid-feedback">Nama sesi wajib diisi.</div>
                        </div>
                        <div class="col-12 col-md-3">
                            <input type="time" name="sessions[0][jam_mulai]" class="form-control" required>
                            <div class="invalid-feedback">Jam mulai wajib diisi.</div>
                        </div>
                        <div class="col-12 col-md-3">
                            <input type="time" name="sessions[0][jam_selesai]" class="form-control" required>
                            <div class="invalid-feedback">Jam selesai wajib diisi.</div>
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
function attachCertAutoCompress(inputId) {
    const fileInput = document.getElementById(inputId);
    if (!fileInput) return;

    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        const maxSize = 8 * 1024 * 1024;
        if (file.size <= maxSize) return;
        if (!file.type.match(/^image\/(jpeg|png|jpg|webp)$/)) return;

        try {
            const reader = new FileReader();
            reader.onload = function(event) {
                const img = new Image();
                img.onload = function() {
                    try {
                        const canvas = document.createElement('canvas');
                        const ctx = canvas.getContext('2d');

                        let width = img.width;
                        let height = img.height;
                        const maxDim = 2200;

                        if (width > maxDim || height > maxDim) {
                            if (width > height) {
                                height = Math.round((height * maxDim) / width);
                                width = maxDim;
                            } else {
                                width = Math.round((width * maxDim) / height);
                                height = maxDim;
                            }
                        }

                        canvas.width = width;
                        canvas.height = height;
                        ctx.drawImage(img, 0, 0, width, height);

                        canvas.toBlob(function(blob) {
                            if (blob && blob.size < file.size) {
                                const newFile = new File([blob], file.name, { type: blob.type });
                                const dataTransfer = new DataTransfer();
                                dataTransfer.items.add(newFile);
                                fileInput.files = dataTransfer.files;
                            }
                        }, 'image/jpeg', 0.85);
                    } catch (err) {
                        console.error('Compression error:', err);
                    }
                };
                img.onerror = function() {
                    console.error('Image load error');
                };
                img.src = event.target.result;
            };
            reader.readAsDataURL(file);
        } catch (err) {
            console.error('File read error:', err);
        }
    });
}
attachCertAutoCompress('cert_template_c');

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
                <div class="invalid-feedback">Nama sesi wajib diisi.</div>
            </div>
            <div class="col-12 col-md-3">
                <input type="time" name="sessions[${sessionCount}][jam_mulai]" class="form-control" required>
                <div class="invalid-feedback">Jam mulai wajib diisi.</div>
            </div>
            <div class="col-12 col-md-3">
                <input type="time" name="sessions[${sessionCount}][jam_selesai]" class="form-control" required>
                <div class="invalid-feedback">Jam selesai wajib diisi.</div>
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

const createEventForm = document.getElementById('createEventForm');
createEventForm.addEventListener('submit', function(e) {
    if (!this.checkValidity()) {
        const invalidFields = this.querySelectorAll(':invalid');
        invalidFields.forEach(el => el.classList.add('is-invalid'));
        if (invalidFields.length) {
            invalidFields[0].closest('.card')?.classList.add('border-danger');
        }
    }
});
createEventForm.addEventListener('input', function(e) {
    if (e.target.matches('input, select')) {
        e.target.classList.toggle('is-invalid', !e.target.checkValidity());
    }
});
</script>
@endpush
@endsection
