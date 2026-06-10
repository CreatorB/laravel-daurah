<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventSession;
use App\Models\QrToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MonitorController extends Controller
{
    public function index($eventId)
    {
        $event = Event::with('sessions')->findOrFail($eventId);
        return view('monitor.index', compact('event'));
    }

    public function generateQr($eventId)
    {
        $event = Event::findOrFail($eventId);
        
        if ($event->qr_mode === 'static') {
            $url = url('/proses-scan/' . $eventId . '?type=static');
            $qrImage = 'https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=' . urlencode($url);
            return response()->json(['html' => '<img src="' . $qrImage . '" alt="QR Code" />']);
        }
        
        $token = Str::random(32);
        $expiresAt = now()->addSeconds(20);
        
        QrToken::create([
            'token' => $token,
            'event_id' => $eventId,
            'expires_at' => $expiresAt,
        ]);
        
        $url = url('/proses-scan/' . $eventId . '?token=' . $token);
        $qrImage = 'https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=' . urlencode($url);
        
        return response()->json(['html' => '<img src="' . $qrImage . '" alt="QR Code" />']);
    }

    public function getSessionInfo($eventId)
    {
        $now = date('H:i:s');
        $today = date('Y-m-d');
        
        $session = EventSession::where('event_id', $eventId)
            ->where('jam_mulai', '<=', $now)
            ->where('jam_selesai', '>=', $now)
            ->first();
            
        if (!$session) {
            $session = EventSession::where('event_id', $eventId)
                ->where('jam_mulai', '>', $now)
                ->orderBy('jam_mulai', 'asc')
                ->first();
 }
        
        if ($session) {
            $status = ($now >= $session->jam_mulai && $now <= $session->jam_selesai) ? 'AKTIF' : 'MENDATANG';
            $html = '<i class="fa fa-clock me-1"></i> ' . $session->nama_sesi . ' <span class="badge bg-' . ($status === 'AKTIF' ? 'success' : 'warning') . '">' . $status . '</span>';
 } else {
            $html = '<i class="fa fa-moon me-1"></i> Tidak Ada Sesi';
        }
        
        return response()->json(['html' => $html]);
    }
}
