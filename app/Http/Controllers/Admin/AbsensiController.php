<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
            fputcsv($file, ['No', 'Sesi', 'Jam Sesi', 'Nama', 'Lembaga', 'Domisili', 'Waktu Absen', 'Konfirmasi Materi']);

            $no = 1;
            foreach ($sessions as $sessionRow) {
                foreach ($sessionRow['attendances'] as $row) {
                    fputcsv($file, [
                        $no++,
                        $sessionRow['nama_sesi'],
                        $sessionRow['jam_range'],
                        $row['nama'],
                        $row['lembaga'],
                        $row['domisili'],
                        $row['waktu_scan_readable'],
                        $row['materi_confirmed'] ? 'Sudah' : 'Belum',
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportExcel($eventId)
    {
        $event = Event::with('sessions')->findOrFail($eventId);
        $sessions = $this->buildSessionRows($event);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Absensi');

        $sheet->fromArray(
            ['No', 'Sesi', 'Jam Sesi', 'Nama', 'Lembaga', 'Domisili', 'Waktu Absen', 'Konfirmasi Materi'],
            null,
            'A1'
        );
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);

        $rowIndex = 2;
        $no = 1;
        foreach ($sessions as $sessionRow) {
            foreach ($sessionRow['attendances'] as $row) {
                $sheet->fromArray([
                    $no++,
                    $sessionRow['nama_sesi'],
                    $sessionRow['jam_range'],
                    $row['nama'],
                    $row['lembaga'],
                    $row['domisili'],
                    $row['waktu_scan_readable'],
                    $row['materi_confirmed'] ? 'Sudah' : 'Belum',
                ], null, 'A' . $rowIndex);
                $rowIndex++;
            }
        }

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $slug = Str::slug($event->nama_event);
        $filename = 'absensi_' . $slug . '_' . date('Ymd') . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
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
