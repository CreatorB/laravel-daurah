@extends('layouts.app')

@section('title', 'Dashboard - Daurah Syariyyah')

@section('content')
<div class="container">
    @if(session('success'))
    <div class="alert alert-success d-flex align-items-center gap-2">
        <i class="fas fa-check-circle fa-lg"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger d-flex align-items-center gap-2">
        <i class="fas fa-exclamation-circle fa-lg"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    {{-- ===== KARTU IDENTITAS PESERTA (untuk ditunjukkan ke panitia) ===== --}}
    <div class="card mb-3 welcome-card" style="border-radius:20px; overflow:hidden;">
        <div class="card-body p-0">
            <div style="background: linear-gradient(135deg, #0369a1, #0ea5e9, #38bdf8); padding:20px 20px 0 20px;">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                    <div>
                        <p class="mb-1 text-white" style="font-size:0.85rem; opacity:0.9;">
                            <i class="fas fa-clock me-1"></i>{{ \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->isoFormat('dddd, D MMMM YYYY') }} - {{ \Carbon\Carbon::now('Asia/Jakarta')->format('H:i') }} WIB
                        </p>
                        <p class="mb-1 text-white" style="font-size:0.78rem; opacity:0.85; letter-spacing:1px; text-transform:uppercase;">Assalamu'alaikum</p>
                        <h2 class="text-white fw-bold mb-0" style="font-size:clamp(1.4rem, 5vw, 2.2rem); line-height:1.2;">{{ $user->nama }}</h2>
                    </div>
                    <a href="{{ route('user.profile') }}" class="btn btn-light btn-sm" style="white-space:nowrap; border-radius:50px!important;">
                        <i class="fas fa-user-edit me-1"></i>Edit Profil
                    </a>
                </div>
            </div>

            {{-- Info kartu besar untuk panitia --}}
            <div style="background: rgba(255,255,255,0.12); backdrop-filter:blur(10px); padding:14px 20px; border-top: 1px solid rgba(255,255,255,0.2);">
                <div class="row g-2">
                    <div class="col-6 col-md-3">
                        <div class="text-center" style="background:rgba(255,255,255,0.15); border-radius:12px; padding:10px 6px;">
                            <i class="fas fa-phone-alt text-white mb-1" style="font-size:1.1rem;"></i>
                            <a href="https://wa.me/{{ str_replace('0', '62', $user->nohp) }}" target="_blank" class="text-white fw-bold text-decoration-none" style="font-size:clamp(0.8rem, 3vw, 1rem); word-break:break-all;">{{ $user->nohp }}</a>
                            <div class="text-white" style="font-size:0.65rem; opacity:0.8;">No. WhatsApp</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center" style="background:rgba(255,255,255,0.15); border-radius:12px; padding:10px 6px;">
                            <i class="fas fa-building text-white mb-1" style="font-size:1.1rem;"></i>
                            <div class="text-white fw-bold" style="font-size:clamp(0.8rem, 3vw, 1rem); word-break:break-word;">{{ $user->lembaga ?: 'PRIBADI' }}</div>
                            <div class="text-white" style="font-size:0.65rem; opacity:0.8;">Lembaga</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center" style="background:rgba(255,255,255,0.15); border-radius:12px; padding:10px 6px;">
                            <i class="fas fa-map-marker-alt text-white mb-1" style="font-size:1.1rem;"></i>
                            <div class="text-white fw-bold" style="font-size:clamp(0.8rem, 3vw, 1rem); word-break:break-word;">{{ $user->domisili ?: '-' }}</div>
                            <div class="text-white" style="font-size:0.65rem; opacity:0.8;">Domisili</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center" style="background:rgba(255,255,255,0.15); border-radius:12px; padding:10px 6px;">
                            <i class="fas fa-bed text-white mb-1" style="font-size:1.1rem;"></i>
                            <div class="text-white fw-bold" style="font-size:clamp(0.8rem, 3vw, 1rem);">
                                {{ $user->menginap === 'ya' ? 'Menginap' : 'Tidak' }}
                            </div>
                            <div class="text-white" style="font-size:0.65rem; opacity:0.8;">Penginapan</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Status registrasi event --}}
            @if($myEvents->count() > 0)
            <div style="padding:10px 20px 14px 20px; background:rgba(255,255,255,0.06);">
                @foreach($myEvents as $reg)
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-1" style="background:rgba(255,255,255,0.12); border-radius:10px; padding:8px 12px; margin-bottom:4px;">
                    <div>
                        <span class="text-white fw-semibold" style="font-size:0.85rem;">{{ $reg->event->nama_event }}</span>
                        <span class="text-white ms-2" style="font-size:0.75rem; opacity:0.8;">
                            {{ date('d M Y', strtotime($reg->event->tanggal)) }}
                        </span>
                    </div>
                    @if($reg->status === 'confirmed')
                    <span style="background:#10b981; color:white; padding:3px 10px; border-radius:20px; font-size:0.72rem; font-weight:700; white-space:nowrap;">
                        <i class="fas fa-check-circle me-1"></i>TERKONFIRMASI
                    </span>
                    @elseif(is_null($reg->status))
                    <span style="background:#6366f1; color:white; padding:3px 10px; border-radius:20px; font-size:0.72rem; font-weight:700; white-space:nowrap;">
                        <i class="fas fa-globe me-1"></i>AUTO INVITE
                    </span>
                    @else
                    <span style="background:rgba(251,191,36,0.9); color:#92400e; padding:3px 10px; border-radius:20px; font-size:0.72rem; font-weight:700; white-space:nowrap;">
                        <i class="fas fa-clock me-1"></i>MENUNGGU
                    </span>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- ===== HIGHLIGHT GRUP LINK (untuk peserta yang sudah konfirm) ===== --}}
    @php
    $confirmedWithGroup = $myEvents->filter(fn($r) => ($r->status === 'confirmed' || ($r->status === null && $r->event->auto_invite)) && !empty($r->event->group_link))->first();
    @endphp
    @if($confirmedWithGroup)
    <div style="background: linear-gradient(135deg, #065f46, #059669, #34d399); border-radius:16px; padding:16px 18px; margin-bottom:16px; box-shadow: 0 8px 25px rgba(16,185,129,0.35);">
        <div class="d-flex align-items-start gap-3">
            <div style="background:rgba(255,255,255,0.2); border-radius:12px; padding:10px; flex-shrink:0;">
                <i class="fab fa-whatsapp text-white" style="font-size:1.6rem;"></i>
            </div>
            <div class="flex-grow-1">
                <div class="text-white fw-bold mb-1" style="font-size:0.95rem;">
                    <i class="fas fa-exclamation-circle me-1"></i>Segera Bergabung Grup Komunikasi!
                </div>
                <div class="text-white mb-2" style="font-size:0.8rem; opacity:0.9;">
                    Jangan sampai ketinggalan informasi penting seputar acara <strong>{{ $confirmedWithGroup->event->nama_event }}</strong>.
                </div>
                <a href="{{ $confirmedWithGroup->event->group_link }}" target="_blank"
                   style="background:white; color:#065f46; padding:8px 16px; border-radius:30px; font-weight:700; font-size:0.8rem; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                    <i class="fab fa-whatsapp"></i> Bergabung Sekarang
                </a>
            </div>
        </div>
    </div>
    @endif

    {{-- ===== SESI AKTIF ===== --}}
    @if($activeSession)
    <div class="card mb-3 border-0 overflow-hidden" style="border-radius:20px;">
        <div class="card-header gradient-bg py-3">
            <i class="fas fa-broadcast-tower me-2"></i>SESI AKTIF SEKARANG
        </div>
        <div class="card-body p-3 p-md-4">
            <h5 class="card-title fw-bold" style="font-size:1.1rem;">{{ $activeSession->nama_sesi }}</h5>
            <p class="card-text mb-3" style="font-size:0.9rem;">
                <strong>{{ $currentEvent->nama_event }}</strong><br>
                <i class="fas fa-calendar me-1"></i>{{ date('d F Y', strtotime($currentEvent->tanggal)) }}<br>
                <i class="fas fa-clock me-1"></i>{{ substr($activeSession->jam_mulai, 0, 5) }} - {{ substr($activeSession->jam_selesai, 0, 5) }}
            </p>

            @php
            $alreadyAbsen = $attendances->where('session_id', $activeSession->id)->count() > 0;
            @endphp

            @if($alreadyAbsen)
            <div class="alert alert-success mb-0 d-flex align-items-center gap-2">
                <i class="fas fa-check-circle fa-lg"></i>
                <span>Anda sudah absen untuk sesi ini. Jazakallahu khairan!</span>
            </div>
            @else
            <form action="{{ route('user.absen') }}" method="POST" id="absenForm">
                @csrf
                <input type="hidden" name="event_id" value="{{ $currentEvent->id }}">
                <input type="hidden" name="session_id" value="{{ $activeSession->id }}">
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">

                <div id="locationStatus" class="mb-3"></div>

                <button type="button" class="btn btn-hadir w-100 pulse text-white" onclick="getLocationAndSubmit()">
                    <i class="fas fa-map-marker-alt me-2"></i>ABSEN SEKARANG
                </button>
            </form>
            @endif

            {{-- Konfirmasi Materi --}}
            @if($currentEvent->material_type !== 'none')
            @php
            $attendance = $attendances->where('session_id', $activeSession->id)->first();
            $eventConfirmed = $eventMateriConfirmed[$currentEvent->id] ?? false;
            $isFirstAttendance = $attendance && ($eventFirstAttendanceId[$currentEvent->id] ?? null) === $attendance->id;
            $canConfirmHere = $attendance && ($currentEvent->material_type === 'per_session' || $isFirstAttendance);
            @endphp
            @if($attendance && $canConfirmHere && !$attendance->materi_confirmed && !$eventConfirmed)
            <form action="{{ route('user.materi') }}" method="POST" class="mt-3">
                @csrf
                <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="fas fa-book me-2"></i>Konfirmasi Pengambilan Materi
                </button>
            </form>
            @elseif($attendance && ($attendance->materi_confirmed || $eventConfirmed))
            <div class="mt-3 text-center" style="color:#0ea5e9; font-size:0.85rem;">
                <i class="fas fa-book-open me-1"></i> Materi sudah dikonfirmasi
            </div>
            @endif
            @endif
        </div>
    </div>
    @endif

    {{-- ===== SESI BERIKUTNYA ===== --}}
    @if($nextSession && !$activeSession)
    @php
    $sessionEndAt = \Carbon\Carbon::parse($currentEvent->tanggal . ' ' . $nextSession->jam_selesai, 'Asia/Jakarta');
    $sessionEnded = \Carbon\Carbon::now('Asia/Jakarta')->greaterThan($sessionEndAt);
    @endphp
    <div class="card mb-3 border-0 overflow-hidden" style="border-radius:20px;">
        <div class="card-header py-3" style="background: linear-gradient(135deg, {{ $sessionEnded ? '#6b7280,#9ca3af' : '#f59e0b,#fbbf24' }}); color:{{ $sessionEnded ? '#ffffff' : '#1c1917' }};">
            <i class="fas {{ $sessionEnded ? 'fa-check-circle' : 'fa-calendar-alt' }} me-2"></i>{{ $sessionEnded ? 'SESI SELESAI' : 'SESI BERIKUTNYA' }}
        </div>
        <div class="card-body p-3 p-md-4">
            <h5 class="card-title fw-bold" style="font-size:1.1rem;">{{ $nextSession->nama_sesi }}</h5>
            <p class="card-text" style="font-size:0.9rem;">
                <strong>{{ $currentEvent->nama_event }}</strong><br>
                <i class="fas fa-calendar me-1"></i>{{ date('d F Y', strtotime($currentEvent->tanggal)) }}<br>
                <i class="fas fa-clock me-1"></i>{{ substr($nextSession->jam_mulai, 0, 5) }} - {{ substr($nextSession->jam_selesai, 0, 5) }}
            </p>
            @if($sessionEnded)
            <div class="alert mb-0" style="background:linear-gradient(135deg,#e5e7eb,#d1d5db); color:#374151; border:none;">
                <i class="fas fa-check-double me-1"></i>Daurah telah selesai. Terima kasih partisipasinya, Jazakallahu khairan.
            </div>
            @else
            <div class="alert mb-0" style="background:linear-gradient(135deg,#fef3c7,#fde68a); color:#92400e; border:none;">
                <i class="fas fa-bell me-1"></i>Harap bersiap dan hadir tepat waktu, inshaAllah.
            </div>
            @endif

        </div>
    </div>
    @endif

    {{-- ===== RIWAYAT KEHADIRAN ===== --}}
    @if($attendances->count() > 0)
    <div class="card mb-3">
        <div class="card-header gradient-bg">
            <i class="fas fa-history me-2"></i>Riwayat Kehadiran
        </div>
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                @foreach($attendances as $att)
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-semibold" style="font-size:0.9rem;">{{ $att->session->nama_sesi ?? 'Sesi' }}</h6>
                            <small class="text-muted" style="font-size:0.78rem;">
                                {{ $att->event->nama_event ?? 'Event' }} &bull;
                                {{ \Carbon\Carbon::parse($att->waktu_scan)->setTimezone('Asia/Jakarta')->locale('id')->isoFormat('dddd, D MMMM YYYY, HH:mm') }} WIB
                            </small>
                        </div>
                        <div class="d-flex flex-column align-items-end gap-1">
                            <span class="badge" style="background:linear-gradient(135deg,#10b981,#34d399); font-size:0.68rem;">
                                <i class="fas fa-check me-1"></i>Hadir
                            </span>
                            @php
                            $rowEventConfirmed = $eventMateriConfirmed[$att->event_id] ?? false;
                            $rowIsFirstAttendance = ($eventFirstAttendanceId[$att->event_id] ?? null) === $att->id;
                            $rowCanConfirm = $att->event && ($att->event->material_type === 'per_session' || $rowIsFirstAttendance);
                            @endphp
                            @if($att->materi_confirmed || $rowEventConfirmed)
                            <span class="badge" style="background:linear-gradient(135deg,#0ea5e9,#38bdf8); font-size:0.68rem;">
                                <i class="fas fa-book me-1"></i>Materi
                            </span>
                            @elseif($att->event && $att->event->material_type !== 'none' && $rowCanConfirm)
                            <form action="{{ route('user.materi') }}" method="POST">
                                @csrf
                                <input type="hidden" name="attendance_id" value="{{ $att->id }}">
                                <button type="submit" class="badge border-0" style="background:linear-gradient(135deg,#f59e0b,#fbbf24); color:#1c1917; font-size:0.68rem; cursor:pointer;">
                                    <i class="fas fa-book me-1"></i>Ambil Materi
                                </button>
                            </form>
                            @endif

                            @php
                            $rowIsLastAttendance = ($eventLastAttendanceId[$att->event_id] ?? null) === $att->id;
                            $rowAllSessionsAttended = $eventAllSessionsAttended[$att->event_id] ?? false;
                            $rowHasCertTemplate = $att->event && $att->event->cert_enabled && !empty($att->event->cert_template);
                            @endphp
                            @if($rowIsLastAttendance && $rowAllSessionsAttended && $rowHasCertTemplate)
                            <a href="{{ route('certificate', ['event_id' => $att->event_id]) }}" class="badge border-0 text-decoration-none" style="background:linear-gradient(135deg,#7c3aed,#a855f7); font-size:0.68rem;">
                                <i class="fas fa-certificate me-1"></i>Generate Sertifikat
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- ===== SERTIFIKAT ===== --}}
    @if($certificates && $certificates->count() > 0)
    <div class="card mt-3">
        <div class="card-header gradient-bg">
            <i class="fas fa-certificate me-2"></i>Sertifikat Saya
        </div>
        <div class="card-body">
            <div class="row g-2">
                @foreach($certificates as $cert)
                <div class="col-12 col-md-6">
                    <div class="border rounded-3 p-3 d-flex justify-content-between align-items-center" style="background:linear-gradient(135deg,#f0f9ff,#e0f2fe);">
                        <div style="flex:1;">
                            <h6 class="fw-bold mb-1" style="font-size:0.95rem;">{{ $cert->nama_event }}</h6>
                            <small class="text-muted" style="font-size:0.75rem;">
                                <i class="fas fa-calendar me-1"></i>{{ date('d M Y', strtotime($cert->tanggal)) }}
                            </small>
                        </div>
                        <a href="{{ route('certificate', ['event_id' => $cert->id]) }}" class="btn btn-primary btn-sm ms-2" style="white-space:nowrap;">
                            <i class="fas fa-print me-1"></i>Cetak
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
function getLocationAndSubmit() {
    const statusDiv = document.getElementById('locationStatus');

    if (!navigator.geolocation) {
        statusDiv.innerHTML = '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>Geolocation tidak didukung browser ini</div>';
        return;
    }

    statusDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-spinner fa-spin me-2"></i>Mendapatkan lokasi, mohon tunggu...</div>';

    navigator.geolocation.getCurrentPosition(
        function(position) {
            document.getElementById('latitude').value = position.coords.latitude;
            document.getElementById('longitude').value = position.coords.longitude;
            statusDiv.innerHTML = '<div class="alert alert-success"><i class="fas fa-check me-2"></i>Lokasi ditemukan! Mengirim absen...</div>';
            document.getElementById('absenForm').submit();
        },
        function(error) {
            let msg = 'Gagal mendapatkan lokasi';
            switch(error.code) {
                case error.PERMISSION_DENIED: msg = 'Izin lokasi ditolak. Silakan izinkan akses lokasi di browser.'; break;
                case error.POSITION_UNAVAILABLE: msg = 'Lokasi tidak tersedia saat ini.'; break;
                case error.TIMEOUT: msg = 'Waktu habis saat mendapatkan lokasi.'; break;
            }
            statusDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-times-circle me-2"></i>' + msg + '</div>';
        },
        { timeout: 10000, maximumAge: 0 }
    );
}
</script>
@endpush
@endsection
