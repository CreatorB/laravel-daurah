<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Attendance;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

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
        
        if (!$event || empty($event->cert_template)) {
            return redirect()->route('user.dashboard')->with('error', 'Template sertifikat tidak tersedia.');
        }
        
        $user = \App\Models\User::find($userId);
        
        return view('user.certificate', compact('event', 'user'));
    }
}
