<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_events' => Event::count(),
            'total_users' => \App\Models\User::where('role', 'user')->count(),
            'pending_confirmations' => EventRegistration::where('status', 'pending')->count(),
            'total_attendances' => Attendance::count(),
        ];

        $recentEvents = Event::orderBy('tanggal', 'desc')->limit(5)->get();
 $pendingRegistrations = EventRegistration::with(['user', 'event'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentEvents', 'pendingRegistrations'));
    }
}
