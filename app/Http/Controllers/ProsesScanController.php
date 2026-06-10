<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventSession;
use App\Models\Attendance;
use App\Models\QrToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class ProsesScanController extends Controller
{
    public function proses(Request $request, $eventId)
    {
        $type = $request->get('type');
        $token = $request->get('token');
        
        $event = Event::findOrFail($eventId);
        
        if ($event->qr_mode === 'dynamic' && $type !== 'static') {
            if (!$token) {
                return view('scan.error', ['message' => 'Token tidak valid atau expired.']);
            }
            
            $qrToken = QrToken::where('token', $token)
                ->where('event_id', $eventId)
                ->where('expires_at', '>', now())
                ->first();
                
            if (!$qrToken) {
                return view('scan.error', ['message' => 'Token tidak valid atau expired.']);
            }
        }
        
        if (!Session::has('user_id')) {
            return redirect()->route('login');
        }
        
        $userId = Session::get('user_id');
        $now = date('H:i:s');
        $today = date('Y-m-d');
        
        $session = EventSession::where('event_id', $eventId)
            ->where('jam_mulai', '<=', $now)
            ->where('jam_selesai', '>=', $now)
            ->first();
            
        if (!$session) {
            return view('scan.error', ['message' => 'Tidak ada sesi yang aktif saat ini.']);
        }
        
        $alreadyAbsen = Attendance::where('user_id', $userId)
            ->where('session_id', $session->id)
            ->exists();
            
        if ($alreadyAbsen) {
            return view('scan.success', [
                'message' => 'Anda sudah absen untuk sesi ini!',
                'event' => $event,
                'session' => $session,
            ]);
        }
        
        Attendance::create([
            'user_id' => $userId,
            'event_id' => $eventId,
            'session_id' => $session->id,
            'waktu_scan' => now(),
        ]);
        
        return view('scan.success', [
            'message' => 'Alhamdulillah, kehadiran Anda berhasil dicatat!',
            'event' => $event,
            'session' => $session,
        ]);
    }
}
