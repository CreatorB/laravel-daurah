<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventSession;
use App\Models\EventRegistration;
use App\Models\Attendance;
use App\Models\Material;
use App\Models\RecycleBin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;

class RecycleBinController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'active');

        $query = RecycleBin::query();

        if ($request->filled('entity_type')) {
            $query->where('entity_type', $request->entity_type);
        }

        if ($request->filled('start_date')) {
            $query->where('deleted_at', '>=', $request->start_date . ' 00:00:00');
        }

        if ($request->filled('end_date')) {
            $query->where('deleted_at', '<=', $request->end_date . ' 23:59:59');
        }

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('label', 'like', '%' . $keyword . '%')
                  ->orWhere('deleted_by_name', 'like', '%' . $keyword . '%');
            });
        }

        switch ($tab) {
            case 'restored':
                $query->whereNotNull('restored_at')->whereNull('permanently_deleted_at');
                break;
            case 'permanent':
                $query->whereNotNull('permanently_deleted_at');
                break;
            case 'all':
                break;
            case 'active':
            default:
                $query->whereNull('restored_at')->whereNull('permanently_deleted_at');
                break;
        }

        $items = $query->orderByDesc('deleted_at')->paginate(25)->withQueryString();

        $counts = [
            'active' => RecycleBin::whereNull('restored_at')->whereNull('permanently_deleted_at')->count(),
            'restored' => RecycleBin::whereNotNull('restored_at')->whereNull('permanently_deleted_at')->count(),
            'permanent' => RecycleBin::whereNotNull('permanently_deleted_at')->count(),
            'all' => RecycleBin::count(),
        ];

        $activeFilters = [
            'tab' => $tab,
            'entity_type' => $request->entity_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'keyword' => $request->keyword,
        ];

        return view('admin.recycle-bin.index', compact('items', 'counts', 'activeFilters', 'tab'));
    }

    public function show($id)
    {
        $item = RecycleBin::findOrFail($id);
        $entity = $item->entity();
        return view('admin.recycle-bin.show', compact('item', 'entity'));
    }

    public function restore($id)
    {
        $item = RecycleBin::findOrFail($id);

        if ($item->restored_at) {
            return redirect()->route('admin.recycle-bin.index', ['tab' => 'restored'])
                ->with('error', 'Item ini sudah direstore sebelumnya.');
        }

        if ($item->permanently_deleted_at) {
            return redirect()->route('admin.recycle-bin.index', ['tab' => 'permanent'])
                ->with('error', 'Item ini sudah dihapus permanen.');
        }

        $entity = $this->resolveEntity($item);

        if (!$entity) {
            return redirect()->route('admin.recycle-bin.index')
                ->with('error', 'Record asli tidak ditemukan, tidak dapat direstore.');
        }

        DB::beginTransaction();
        try {
            if ($item->entity_type === Event::class) {
                $this->restoreEventFromSnapshot($item, $entity);
            } elseif ($item->entity_type === User::class) {
                $entity->restore();
            }

            $item->update([
                'restored_at' => now(),
                'restored_by_name' => Session::get('nama') ?? 'admin',
            ]);

            DB::commit();
            return redirect()->route('admin.recycle-bin.index')->with('success', 'Item berhasil direstore.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.recycle-bin.index')->with('error', 'Gagal restore: ' . $e->getMessage());
        }
    }

    public function forceDestroy($id)
    {
        $item = RecycleBin::findOrFail($id);

        if ($item->permanently_deleted_at) {
            return redirect()->route('admin.recycle-bin.index', ['tab' => 'permanent'])
                ->with('error', 'Item ini sudah dihapus permanen.');
        }

        if ($item->restored_at) {
            $this->cleanupRestoredChildren($item);
            $item->delete();
            return redirect()->route('admin.recycle-bin.index')->with('success', 'Catatan recycle bin telah dihapus.');
        }

        DB::beginTransaction();
        try {
            $entity = $this->resolveEntity($item);

            if ($item->entity_type === Event::class) {
                $this->cascadeHardDeleteEvent($item, $entity);
            } elseif ($item->entity_type === User::class) {
                $this->cascadeHardDeleteUser($item, $entity);
            }

            $item->update([
                'permanently_deleted_at' => now(),
                'permanently_deleted_by_name' => Session::get('nama') ?? 'admin',
            ]);

            DB::commit();
            return redirect()->route('admin.recycle-bin.index')->with('success', 'Item dihapus permanen.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.recycle-bin.index')->with('error', 'Gagal menghapus permanen: ' . $e->getMessage());
        }
    }

    public function purge($id)
    {
        $item = RecycleBin::findOrFail($id);
        if (!$item->permanently_deleted_at && !$item->restored_at) {
            return redirect()->route('admin.recycle-bin.index')
                ->with('error', 'Hapus permanen dulu sebelum membersihkan catatan.');
        }
        $item->delete();
        return redirect()->route('admin.recycle-bin.index', ['tab' => $item->restored_at ? 'restored' : 'permanent'])
            ->with('success', 'Catatan recycle bin telah dihapus.');
    }

    private function resolveEntity(RecycleBin $item)
    {
        $class = $item->entity_type;
        if (!class_exists($class)) {
            return null;
        }
        return $class::withTrashed()->find($item->entity_id);
    }

    private function restoreEventFromSnapshot(RecycleBin $item, $event)
    {
        if ($event->trashed()) {
            $event->restore();
        }

        $snapshot = $item->snapshot ?? [];

        if (!empty($snapshot['sessions'])) {
            $existingSessionIds = $event->sessions()->pluck('id')->all();
            foreach ($snapshot['sessions'] as $row) {
                if (in_array($row['id'], $existingSessionIds)) {
                    continue;
                }
                EventSession::create([
                    'event_id' => $event->id,
                    'nama_sesi' => $row['nama_sesi'] ?? null,
                    'jam_mulai' => $row['jam_mulai'] ?? null,
                    'jam_selesai' => $row['jam_selesai'] ?? null,
                ]);
            }
        }

        if (!empty($snapshot['registrations'])) {
            $existingRegIds = EventRegistration::where('event_id', $event->id)->pluck('id')->all();
            foreach ($snapshot['registrations'] as $row) {
                if (in_array($row['id'], $existingRegIds)) {
                    continue;
                }
                EventRegistration::create([
                    'id' => $row['id'],
                    'event_id' => $event->id,
                    'user_id' => $row['user_id'] ?? null,
                    'status' => $row['status'] ?? 'pending',
                    'confirmed_at' => $row['confirmed_at'] ?? null,
                ]);
            }
        }

        if (!empty($snapshot['attendances'])) {
            $existingAttIds = Attendance::where('event_id', $event->id)->pluck('id')->all();
            foreach ($snapshot['attendances'] as $row) {
                if (in_array($row['id'], $existingAttIds)) {
                    continue;
                }
                Attendance::create([
                    'id' => $row['id'],
                    'event_id' => $event->id,
                    'user_id' => $row['user_id'] ?? null,
                    'session_id' => $row['session_id'] ?? null,
                    'status' => $row['status'] ?? null,
                    'waktu_scan' => $row['waktu_scan'] ?? null,
                    'check_in_method' => $row['check_in_method'] ?? null,
                    'latitude' => $row['latitude'] ?? null,
                    'longitude' => $row['longitude'] ?? null,
                ]);
            }
        }

        if (!empty($snapshot['materials'])) {
            $existingMatIds = Material::where('event_id', $event->id)->pluck('id')->all();
            foreach ($snapshot['materials'] as $row) {
                if (in_array($row['id'], $existingMatIds)) {
                    continue;
                }
                Material::create([
                    'id' => $row['id'],
                    'event_id' => $event->id,
                    'nama_materi' => $row['nama_materi'] ?? null,
                    'tipe' => $row['tipe'] ?? null,
                    'file_path' => $row['file_path'] ?? null,
                    'video_url' => $row['video_url'] ?? null,
                    'urutan' => $row['urutan'] ?? 0,
                ]);
            }
        }
    }

    private function cascadeHardDeleteEvent(RecycleBin $item, $event)
    {
        Attendance::where('event_id', $item->entity_id)->delete();
        EventSession::where('event_id', $item->entity_id)->delete();
        EventRegistration::where('event_id', $item->entity_id)->delete();
        Material::where('event_id', $item->entity_id)->delete();
        if ($event) {
            $event->forceDelete();
        } else {
            Event::withTrashed()->where('id', $item->entity_id)->forceDelete();
        }
    }

    private function cascadeHardDeleteUser(RecycleBin $item, $user)
    {
        $userId = $item->entity_id;

        if ($user && $user->bukti_undangan) {
            Storage::disk('public')->delete($user->bukti_undangan);
        } else {
            $trashedUser = User::withTrashed()->find($userId);
            if ($trashedUser && $trashedUser->bukti_undangan) {
                Storage::disk('public')->delete($trashedUser->bukti_undangan);
            }
        }

        EventRegistration::where('user_id', $userId)->delete();
        Attendance::where('user_id', $userId)->delete();

        if ($user) {
            $user->forceDelete();
        } else {
            User::withTrashed()->where('id', $userId)->forceDelete();
        }
    }

    private function cleanupRestoredChildren(RecycleBin $item)
    {
        if ($item->entity_type === Event::class) {
            Attendance::where('event_id', $item->entity_id)->delete();
            EventSession::where('event_id', $item->entity_id)->delete();
            EventRegistration::where('event_id', $item->entity_id)->delete();
            Material::where('event_id', $item->entity_id)->delete();
        } elseif ($item->entity_type === User::class) {
            EventRegistration::where('user_id', $item->entity_id)->delete();
            Attendance::where('user_id', $item->entity_id)->delete();
        }
    }
}
