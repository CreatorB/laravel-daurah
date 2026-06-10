<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $eventId = $request->get('event_id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        $events = Event::orderBy('tanggal', 'desc')->get();
        
        $query = Attendance::with(['user', 'event', 'session']);
        
        if ($eventId) {
            $query->where('event_id', $eventId);
        }
        
        if ($startDate && $endDate) {
            $query->whereDate('waktu_scan', '>=', $startDate)
                  ->whereDate('waktu_scan', '<=', $endDate);
        } elseif ($startDate) {
            $query->whereDate('waktu_scan', '>=', $startDate);
        }
        
        $attendances = $query->orderBy('waktu_scan', 'desc')->get();
        
        return view('admin.history.index', compact('attendances', 'events', 'eventId', 'startDate', 'endDate'));
    }
}
