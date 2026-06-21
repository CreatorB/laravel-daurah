<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AbsensiController extends Controller
{
    public function index($eventId = null)
    {
        $events = Event::orderBy('tanggal', 'desc')->get();

        $event = null;
        $sessions = collect();

        if ($eventId) {
            $event = Event::with('sessions')->findOrFail($eventId);
            $sessions = $this->buildSessionRows($event);
        }

        return view('admin.absensi.index', compact('events', 'event', 'sessions'));
    }

    public function exportCsv($eventId)
    {
        $event = Event::with('sessions')->findOrFail($eventId);
        $sessions = $this->buildSessionRows($event);

        $slug = Str::slug($event->nama_event);
        $filename = 'absensi_' . $slug . '_' . date('Ymd') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($sessions) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            $header = ['No', 'Nama', 'Lembaga', 'Domisili'];
            foreach ($sessions as $sessionRow) {
                $header[] = $sessionRow['nama_sesi'] . ' (' . $sessionRow['jam_range'] . ')';
            }
            fputcsv($file, $header);

            $users = [];
            foreach ($sessions as $sessionRow) {
                foreach ($sessionRow['attendances'] as $row) {
                    $userId = $row['user_id'];
                    if (! isset($users[$userId])) {
                        $users[$userId] = [
                            'nama' => $row['nama'],
                            'lembaga' => $row['lembaga'],
                            'domisili' => $row['domisili'],
                            'sesi' => [],
                        ];
                    }
                    $users[$userId]['sesi'][$sessionRow['id']] = $row['waktu_scan_readable'];
                }
            }

            $no = 1;
            foreach ($users as $user) {
                $line = [$no++, $user['nama'], $user['lembaga'], $user['domisili']];
                foreach ($sessions as $sessionRow) {
                    $line[] = $user['sesi'][$sessionRow['id']] ?? '-';
                }
                fputcsv($file, $line);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function buildSessionRows(Event $event)
    {
        $attendances = Attendance::where('event_id', $event->id)
            ->with('user')
            ->orderBy('waktu_scan')
            ->get()
            ->groupBy('session_id');

        return $event->sessions->map(function ($session) use ($attendances) {
            $sessionAttendances = $attendances->get($session->id, collect());

            return [
                'id' => $session->id,
                'nama_sesi' => $session->nama_sesi,
                'jam_range' => substr($session->jam_mulai, 0, 5) . ' - ' . substr($session->jam_selesai, 0, 5),
                'attendances' => $sessionAttendances->map(function ($att) {
                    $waktu = $att->waktu_scan ? Carbon::parse($att->waktu_scan)->setTimezone('Asia/Jakarta') : null;

                    return [
                        'user_id' => $att->user_id,
                        'nama' => $att->user->nama ?? '-',
                        'lembaga' => $att->user->lembaga ?? 'PRIBADI',
                        'domisili' => $att->user->domisili ?? '-',
                        'waktu_scan' => $waktu,
                        'waktu_scan_readable' => $waktu ? $waktu->locale('id')->isoFormat('dddd, D MMMM YYYY, HH:mm:ss') : '-',
                        'materi_confirmed' => (bool) $att->materi_confirmed,
                    ];
                })->values(),
            ];
        })->values();
    }
}
