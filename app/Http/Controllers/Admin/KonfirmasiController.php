<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KonfirmasiController extends Controller
{
    protected $waService;

    public function __construct(WhatsAppService $waService)
    {
        $this->waService = $waService;
    }

    public function index($eventId = null)
    {
        if ($eventId) {
            $event = Event::findOrFail($eventId);
            $registrations = EventRegistration::where('event_id', $eventId)
                ->with('user', 'event')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $event = null;
            $registrations = EventRegistration::with('user', 'event')
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();
        }

        $events = Event::orderBy('tanggal', 'desc')->get();
        
        return view('admin.konfirmasi.index', compact('registrations', 'events', 'event'));
    }

    public function acc($id)
    {
        $registration = EventRegistration::with('user', 'event')->findOrFail($id);
        
        $registration->update(['status' => 'confirmed']);
        
        return redirect()->back()->with('success', "Konfirmasi untuk {$registration->user->nama} berhasil!");
    }

    public function wa($id)
    {
        $registration = EventRegistration::with('user', 'event.sessions')->findOrFail($id);

        if ($registration->status !== 'confirmed') {
            return redirect()->back()->with('error', 'User harus dikonfirmasi terlebih dahulu sebelum mengirim WA!');
        }

        $sessions = $registration->event->sessions->map(fn($s) => [
            'nama_sesi' => $s->nama_sesi,
            'jam_mulai' => substr($s->jam_mulai, 0, 5),
            'jam_selesai' => substr($s->jam_selesai, 0, 5),
        ])->toArray();

        $waLink = $this->waService->generateKonfirmasiLink(
            $registration->user->nohp,
            $registration->event->nama_event,
            $registration->event->tanggal,
            $registration->event->group_link,
            $sessions
        );

        return redirect()->away($waLink);
    }

    public function hapus($id)
    {
        $registration = EventRegistration::findOrFail($id);
        $nama = $registration->user->nama;
        $registration->delete();
        
        return redirect()->back()->with('success', "Pendaftaran {$nama} berhasil dihapus!");
    }

    public function accAll($eventId)
    {
        EventRegistration::where('event_id', $eventId)
            ->where('status', 'pending')
            ->update(['status' => 'confirmed']);
            
        return redirect()->back()->with('success', 'Semua pendaftar berhasil dikonfirmasi!');
    }

    public function exportCsv($eventId)
    {
        $event = Event::findOrFail($eventId);
        $registrations = EventRegistration::where('event_id', $eventId)
            ->with('user')
            ->get();

        $slug = \Illuminate\Support\Str::slug($event->nama_event);
        $filename = 'konfirmasi_' . $slug . '_' . date('Ymd') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $rows = [];
        $rows[] = ['No', 'Nama', 'Lembaga', 'Domisili', 'No WA', 'Menginap', 'Status'];
        $no = 1;
        foreach ($registrations as $reg) {
            $rows[] = [
                $no++,
                $reg->user->nama,
                $reg->user->lembaga ?? 'PRIBADI',
                $reg->user->domisili ?? '-',
                '+' . $reg->user->nohp,
                $reg->user->menginap === 'ya' ? 'Ya' : 'Tidak',
                $reg->status === 'confirmed' ? 'Terkonfirmasi' : 'Pending',
            ];
        }

        $callback = function () use ($rows) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM for Excel
            foreach ($rows as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
