<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventSession;
use App\Models\EventRegistration;
use App\Models\User;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with('sessions')->orderBy('tanggal', 'desc')->get();
        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_event' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'qr_mode' => 'required|in:static,dynamic',
            'group_link' => 'nullable|url|max:500',
            'radius_active' => 'nullable|boolean',
            'radius_lat' => 'nullable|numeric',
            'radius_lng' => 'nullable|numeric',
            'radius_meters' => 'nullable|integer|min:1',
            'material_type' => 'required|in:per_session,once,none',
            'auto_confirm' => 'nullable|boolean',
            'auto_invite' => 'nullable|boolean',
            'cert_enabled' => 'nullable|boolean',
            'cert_template' => 'nullable|image|max:15360',
            'cert_font' => 'nullable|file|mimes:ttf,otf|max:2048',
            'cert_font_size' => 'nullable|integer|min:1',
            'cert_font_color' => 'nullable|string|max:20',
            'sessions' => 'required|array|min:1',
            'sessions.*.nama_sesi' => 'required|string|max:255',
            'sessions.*.jam_mulai' => 'required',
            'sessions.*.jam_selesai' => 'required|after:sessions.*.jam_mulai',
        ]);

        DB::beginTransaction();

        try {
            $event = Event::create([
                'nama_event' => $request->nama_event,
                'tanggal' => $request->tanggal,
                'qr_mode' => $request->qr_mode,
                'group_link' => $request->group_link,
                'radius_active' => $request->radius_active ? 1 : 0,
                'radius_lat' => $request->radius_lat,
                'radius_lng' => $request->radius_lng,
                'radius_meters' => $request->radius_meters ?: 100,
                'material_type' => $request->material_type,
                'auto_confirm' => $request->auto_confirm ? 1 : 0,
                'auto_invite' => $request->auto_invite ? 1 : 0,
                'cert_enabled' => $request->cert_enabled ? 1 : 0,
                'cert_font_size' => $request->cert_font_size ?: 30,
                'cert_font_color' => $request->cert_font_color ?: '#000000',
            ]);

            $this->storeCertFiles($request, $event);

            foreach ($request->sessions as $session) {
                EventSession::create([
                    'event_id' => $event->id,
                    'nama_sesi' => $session['nama_sesi'],
                    'jam_mulai' => $session['jam_mulai'],
                    'jam_selesai' => $session['jam_selesai'],
                ]);
            }

            if ($request->material_type !== 'none' && $request->has('materi')) {
                foreach ($request->materi as $materi) {
                    if (!empty($materi['nama_materi'])) {
                        Material::create([
                            'event_id' => $event->id,
                            'nama_materi' => $materi['nama_materi'],
                            'deskripsi' => $materi['deskripsi'] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.events.edit', $event->id)->with('success', 'Event berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Event store failed: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $event = Event::with(['sessions', 'materials'])->findOrFail($id);
        $users = User::where('role', 'user')->orderBy('nama')->get();
        $registrations = EventRegistration::where('event_id', $id)->with('user')->get();
        
        return view('admin.events.edit', compact('event', 'users', 'registrations'));
    }

    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $request->validate([
            'nama_event' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'qr_mode' => 'required|in:static,dynamic',
            'group_link' => 'nullable|url|max:500',
            'radius_active' => 'nullable|boolean',
            'radius_lat' => 'nullable|numeric',
            'radius_lng' => 'nullable|numeric',
            'radius_meters' => 'nullable|integer|min:1',
            'material_type' => 'required|in:per_session,once,none',
            'auto_confirm' => 'nullable|boolean',
            'auto_invite' => 'nullable|boolean',
            'cert_enabled' => 'nullable|boolean',
            'cert_template' => 'nullable|image|max:15360',
            'cert_font' => 'nullable|file|mimes:ttf,otf|max:2048',
            'cert_font_size' => 'nullable|integer|min:1',
            'cert_font_color' => 'nullable|string|max:20',
        ]);

        $event->update([
            'nama_event' => $request->nama_event,
            'tanggal' => $request->tanggal,
            'qr_mode' => $request->qr_mode,
            'group_link' => $request->group_link,
            'radius_active' => $request->radius_active ? 1 : 0,
            'radius_lat' => $request->radius_lat,
            'radius_lng' => $request->radius_lng,
            'radius_meters' => $request->radius_meters ?: 100,
            'material_type' => $request->material_type,
            'auto_confirm' => $request->auto_confirm ? 1 : 0,
            'auto_invite' => $request->auto_invite ? 1 : 0,
            'cert_enabled' => $request->cert_enabled ? 1 : 0,
            'cert_font_size' => $request->cert_font_size ?: ($event->cert_font_size ?: 30),
            'cert_font_color' => $request->cert_font_color ?: ($event->cert_font_color ?: '#000000'),
        ]);

        $this->storeCertFiles($request, $event);

        return redirect()->back()->with('success', 'Event berhasil diupdate!');
    }

    private function storeCertFiles(Request $request, Event $event): void
    {
        $eventName = Str::slug($event->nama_event);

        if ($request->hasFile('cert_template')) {
            if ($event->cert_template) {
                $oldPath = str_replace('/storage/', '', $event->cert_template);
                \Storage::disk('public')->delete($oldPath);
            }
            $ext = $request->file('cert_template')->getClientOriginalExtension();
            $filename = $eventName . '_template.' . $ext;
            $path = $request->file('cert_template')->storeAs('certificates', $filename, 'public');
            $event->update(['cert_template' => '/storage/' . $path]);
        }

        if ($request->hasFile('cert_font')) {
            if ($event->cert_font) {
                $oldPath = str_replace('/storage/', '', $event->cert_font);
                \Storage::disk('public')->delete($oldPath);
            }
            $ext = $request->file('cert_font')->getClientOriginalExtension();
            $path = $request->file('cert_font')->storeAs('certificates', $eventName . '_font.' . $ext, 'public');
            $event->update(['cert_font' => '/storage/' . $path]);
        }
    }

    public function destroy($id)
    {
        $event = Event::with(['sessions', 'registrations', 'attendances', 'materials'])->findOrFail($id);
        $event->attendances()->delete();
        $event->sessions()->delete();
        $event->registrations()->delete();
        $event->materials()->delete();
        $event->delete();
        return redirect()->route('admin.events.index')->with('success', 'Event berhasil dihapus!');
    }

    public function addSession(Request $request, $id)
    {
        $request->validate([
            'nama_sesi' => 'required|string|max:255',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
        ]);

        EventSession::create([
            'event_id' => $id,
            'nama_sesi' => $request->nama_sesi,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
        ]);

        return redirect()->back()->with('success', 'Sesi berhasil ditambahkan!');
    }

public function updateSession(Request $request, $id)
    {
        $session = EventSession::findOrFail($id);

        $request->validate([
            'nama_sesi' => 'required|string|max:255',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
        ]);

        $session->update([
            'nama_sesi' => $request->nama_sesi,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
        ]);

        return redirect()->back()->with('success', 'Sesi berhasil diupdate!');
    }

    public function deleteSession($id)
    {
        $session = EventSession::findOrFail($id);
        $session->delete();
        return redirect()->back()->with('success', 'Sesi berhasil dihapus!');
    }

    public function inviteUsers(Request $request, $id)
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
        ]);

        foreach ($request->user_ids as $userId) {
            $exists = EventRegistration::where('event_id', $id)
                ->where('user_id', $userId)
                ->exists();
            
            if (!$exists) {
                EventRegistration::create([
                    'event_id' => $id,
                    'user_id' => $userId,
                    'status' => 'pending',
                ]);
            }
        }

        return redirect()->back()->with('success', 'Undangan berhasil dikirim!');
    }

    public function autoConfirm(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        $event->update(['auto_confirm' => $request->auto_confirm ? 1 : 0]);
        
        if ($request->auto_confirm) {
            EventRegistration::where('event_id', $id)
                ->where('status', 'pending')
                ->update(['status' => 'confirmed']);
        }

        return redirect()->back()->with('success', 'Auto konfirmasi ' . ($request->auto_confirm ? 'diaktifkan' : 'dinonaktifkan') . '!');
    }
}
