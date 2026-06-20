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
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

<form action="{{ route('admin.events.update', $event->id) }}" method="POST" enctype="multipart/form-data">
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

        <div class="card mb-4">
            <div class="card-header">Sertifikat</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="cert_enabled" id="cert_enabled_e" class="form-check-input" value="1" {{ $event->cert_enabled ? 'checked' : '' }} onchange="document.getElementById('cert-settings-e').classList.toggle('d-none', !this.checked)">
                            <label class="form-check-label" for="cert_enabled_e">Aktifkan Sertifikat</label>
                        </div>
                        <small class="text-muted">Jika diaktifkan, peserta yang hadir lengkap di semua sesi dapat generate sertifikat dari riwayat kehadirannya.</small>
                    </div>
                    <div id="cert-settings-e" class="row g-3 {{ $event->cert_enabled ? '' : 'd-none' }}">
                        <div class="col-12 col-md-6">
                            <label class="form-label">Template Sertifikat (gambar)</label>
                            <input type="file" name="cert_template" id="cert_template_e" class="form-control" accept="image/*">
                            @if(!empty($event->cert_template))
                            @php
                            $certPath = str_replace('/storage/', '', $event->cert_template);
                            $certVersion = \Illuminate\Support\Facades\Storage::disk('public')->exists($certPath)
                                ? \Illuminate\Support\Facades\Storage::disk('public')->lastModified($certPath)
                                : time();
                            @endphp
                            <div class="mt-2">
                                <img src="{{ $event->cert_template }}?v={{ $certVersion }}" alt="Template sertifikat" style="max-width:220px; max-height:140px; object-fit:contain; border:1px solid #e0f2fe;">
                                <small class="text-success d-block mt-1"><i class="fas fa-check"></i> <a href="{{ $event->cert_template }}?v={{ $certVersion }}" target="_blank">Lihat ukuran penuh</a></small>
                            </div>
                            @endif
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Font Sertifikat (opsional)</label>
                            <input type="file" name="cert_font" class="form-control" accept=".ttf,.otf">
                            @if(!empty($event->cert_font))
                            <small class="text-success d-block mt-1"><i class="fas fa-check"></i> Sudah ada: <a href="{{ $event->cert_font }}" target="_blank">Lihat font</a></small>
                            @endif
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Ukuran Font</label>
                            <input type="number" name="cert_font_size" class="form-control" value="{{ old('cert_font_size', $event->cert_font_size ?? 30) }}">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Warna Font</label>
                            <input type="color" name="cert_font_color" class="form-control form-control-color" value="{{ old('cert_font_color', $event->cert_font_color ?? '#000000') }}">
                        </div>
                    </div>
                </div>
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
                        <tr data-session-id="{{ $session->id }}">
                            <td>
                                <span class="session-view">{{ $session->nama_sesi }}</span>
                                <input type="text" name="nama_sesi" class="form-control form-control-sm session-edit d-none" value="{{ $session->nama_sesi }}" data-original="{{ $session->nama_sesi }}">
                            </td>
                            <td>
                                <span class="session-view">{{ $session->jam_mulai }}</span>
                                <input type="time" name="jam_mulai" class="form-control form-control-sm session-edit d-none" value="{{ $session->jam_mulai }}" data-original="{{ $session->jam_mulai }}">
                            </td>
                            <td>
                                <span class="session-view">{{ $session->jam_selesai }}</span>
                                <input type="time" name="jam_selesai" class="form-control form-control-sm session-edit d-none" value="{{ $session->jam_selesai }}" data-original="{{ $session->jam_selesai }}">
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary btn-edit-session d-none" onclick="saveSession({{ $session->id }})"><i class="fas fa-check"></i></button>
                                <button type="button" class="btn btn-sm btn-secondary btn-cancel-session d-none" onclick="cancelEdit({{ $session->id }})"><i class="fas fa-times"></i></button>
                                <button type="button" class="btn btn-sm btn-primary btn-show-edit" onclick="showEdit({{ $session->id }})"><i class="fas fa-edit"></i></button>
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
attachCertAutoCompress('cert_template_e');

function updateDiameterEdit() {
    const r = parseFloat(document.getElementById('radius_meters_e').value) || 0;
    const d = r * 2;
    document.getElementById('diameter_e').textContent = d;
    document.getElementById('diameter_km_e').textContent = (d / 1000).toFixed(2);
}

function showEdit(sessionId) {
    const row = document.querySelector(`tr[data-session-id="${sessionId}"]`);
    row.querySelectorAll('.session-view').forEach(el => el.classList.add('d-none'));
    row.querySelectorAll('.session-edit').forEach(el => el.classList.remove('d-none'));
    row.querySelector('.btn-edit-session').classList.remove('d-none');
    row.querySelector('.btn-cancel-session').classList.remove('d-none');
    row.querySelector('.btn-show-edit').classList.add('d-none');
}

function cancelEdit(sessionId) {
    const row = document.querySelector(`tr[data-session-id="${sessionId}"]`);
    row.querySelectorAll('.session-view').forEach(el => el.classList.remove('d-none'));
    row.querySelectorAll('.session-edit').forEach(el => el.classList.add('d-none'));
    row.querySelector('.btn-edit-session').classList.add('d-none');
    row.querySelector('.btn-cancel-session').classList.add('d-none');
    row.querySelector('.btn-show-edit').classList.remove('d-none');
}

function saveSession(sessionId) {
    const row = document.querySelector(`tr[data-session-id="${sessionId}"]`);
    const namaSesi = row.querySelector('input[name="nama_sesi"]').value;
    const jamMulai = row.querySelector('input[name="jam_mulai"]').value;
    const jamSelesai = row.querySelector('input[name="jam_selesai"]').value;
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/events/sessions/${sessionId}/update`;
    form.innerHTML = `
        @csrf
        <input type="hidden" name="nama_sesi" value="${namaSesi}">
        <input type="hidden" name="jam_mulai" value="${jamMulai}">
        <input type="hidden" name="jam_selesai" value="${jamSelesai}">
    `;
    document.body.appendChild(form);
    form.submit();
}
</script>
@endpush
@endsection
