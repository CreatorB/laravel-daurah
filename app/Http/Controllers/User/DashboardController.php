<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventSession;
use App\Models\EventRegistration;
use App\Models\Attendance;
use App\Services\GeolocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    protected $geoService;

    public function __construct(GeolocationService $geoService)
    {
        $this->geoService = $geoService;
    }

    public function index()
    {
        $userId = Session::get('user_id');
        $user = \App\Models\User::find($userId);

        if (!$user) {
            return redirect()->route('login');
        }

        $today = date('Y-m-d');
        $now = date('H:i:s');

        $myEvents = EventRegistration::where('user_id', $userId)
            ->with('event.sessions')
            ->whereHas('event', function($q) use ($today) {
                $q->where('tanggal', '>=', $today);
            })
            ->get()
            ->sortBy(fn($r) => $r->event->tanggal ?? '')
            ->values();

        $autoInviteEvents = Event::where('auto_invite', true)
            ->where('tanggal', '>=', $today)
            ->whereDoesntHave('registrations', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->with('sessions')
            ->get();

        foreach ($autoInviteEvents as $event) {
            $fakeReg = new EventRegistration();
            $fakeReg->event = $event;
            $fakeReg->status = null;
            $fakeReg->event_id = $event->id;
            $fakeReg->user_id = $userId;
            $myEvents->push($fakeReg);
        }

        $myEvents = $myEvents->sortBy(fn($r) => $r->event->tanggal ?? '')->values();

        $activeSession = null;
        $nextSession = null;
        $currentEvent = null;

        foreach ($myEvents as $reg) {
            if ($reg->event->tanggal == $today) {
                foreach ($reg->event->sessions as $session) {
                    if ($now >= $session->jam_mulai && $now <= $session->jam_selesai) {
                        $activeSession = $session;
                        $currentEvent = $reg->event;
                        break;
                    }
                }
            }

            if (!$nextSession && $reg->event->tanggal >= $today) {
                $nextSession = $reg->event->sessions->first();
                $currentEvent = $reg->event;
            }
        }

        $attendances = Attendance::where('user_id', $userId)
            ->with(['event', 'session'])
            ->orderBy('waktu_scan', 'desc')
            ->limit(10)
            ->get();

        $certificates = Event::whereNotNull('cert_template')
            ->where('cert_template', '!=', '')
            ->whereHas('attendances', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('user.dashboard', compact('user', 'myEvents', 'activeSession', 'nextSession', 'currentEvent', 'attendances', 'certificates'));
    }

    public function absen(Request $request)
    {
        $userId = Session::get('user_id');
        $user = \App\Models\User::find($userId);

        $request->validate([
            'event_id' => 'required|exists:events,id',
            'session_id' => 'required|exists:event_sessions,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $event = Event::find($request->event_id);
        $session = EventSession::find($request->session_id);

        if ($event->radius_active) {
            if (!$request->latitude || !$request->longitude) {
                return redirect()->back()->with('error', 'Lokasi harus diaktifkan untuk absen!');
            }

            $distance = $this->geoService->calculateDistance(
                $request->latitude,
                $request->longitude,
                $event->radius_lat,
                $event->radius_lng
            );

            if ($distance > $event->radius_meters) {
                return redirect()->back()->with('error', "Anda di luar radius absen! Jarak: " . round($distance) . "m dari titik lokasi.");
            }
        }

        $exists = Attendance::where('user_id', $userId)
            ->where('session_id', $request->session_id)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Anda sudah absen untuk sesi ini!');
        }

        Attendance::create([
            'user_id' => $userId,
            'event_id' => $request->event_id,
            'session_id' => $request->session_id,
            'waktu_scan' => now(),
        ]);

        return redirect()->back()->with('success', 'Absen berhasil! Selamat mengikuti ' . $session->nama_sesi);
    }

    public function konfirmasiMateri(Request $request)
    {
        $userId = Session::get('user_id');

        $request->validate([
            'attendance_id' => 'required|exists:attendance,id',
        ]);

        $attendance = Attendance::where('id', $request->attendance_id)
            ->where('user_id', $userId)
            ->first();

        if (!$attendance) {
            return redirect()->back()->with('error', 'Data attendance tidak ditemukan!');
        }

        if ($attendance->materi_confirmed) {
            return redirect()->back()->with('error', 'Materi sudah dikonfirmasi!');
        }

        $attendance->update([
            'materi_confirmed' => true,
            'materi_confirmed_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Konfirmasi materi berhasil!');
    }

    public function profile()
    {
        $userId = Session::get('user_id');
        $user = \App\Models\User::find($userId);
        return view('user.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $userId = Session::get('user_id');
        $user = \App\Models\User::find($userId);

        $request->validate([
            'nama' => 'required|string|max:255',
            'lembaga' => 'nullable|string|max:255',
            'domisili' => 'required|string|max:255',
            'nohp' => 'required|string|max:20',
            'menginap' => 'required|in:ya,tidak',
        ]);

        $nohp = $this->formatPhone($request->nohp);

        $existing = \App\Models\User::where('nohp', $nohp)->where('id', '!=', $userId)->first();
        if ($existing) {
            return redirect()->back()->with('error', 'Nomor WhatsApp sudah digunakan user lain!');
        }

        $user->update([
            'nama' => $request->nama,
            'lembaga' => $request->lembaga ?: 'PRIBADI',
            'domisili' => $request->domisili,
            'nohp' => $nohp,
            'menginap' => $request->menginap,
        ]);

        Session::put('nama', $user->nama);
        Session::put('nohp', $user->nohp);

        return redirect()->route('user.profile')->with('success', 'Profil berhasil diupdate!');
    }

    private function formatPhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (substr($phone, 0, 1) === '0') {
            return '62' . substr($phone, 1);
        }

        if (substr($phone, 0, 2) === '62') {
            return $phone;
        }

        return '62' . $phone;
    }
}
