<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Attendance;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $userId = Session::get('user_id');
        
        $eventId = $request->get('event_id');
        
        $cekHadir = Attendance::where('user_id', $userId)
            ->where('event_id', $eventId)
            ->exists();
            
        if (!$cekHadir) {
            return redirect()->route('user.dashboard')->with('error', 'Anda belum hadir di event ini.');
        }
        
        $event = Event::find($eventId);

        if (!$event || !$event->cert_enabled || empty($event->cert_template)) {
            return redirect()->route('user.dashboard')->with('error', 'Sertifikat tidak tersedia untuk event ini.');
        }
        
        $user = \App\Models\User::find($userId);

        $certPath = str_replace('/storage/', '', $event->cert_template);
        $certVersion = Storage::disk('public')->exists($certPath)
            ? Storage::disk('public')->lastModified($certPath)
            : time();

        return view('user.certificate', compact('event', 'user', 'certVersion'));
    }
}
