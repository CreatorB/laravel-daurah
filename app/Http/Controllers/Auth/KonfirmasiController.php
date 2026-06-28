<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class KonfirmasiController extends Controller
{
    public function showForm()
    {
        $event = Event::first();

        if ($event && $event->konfirmasi_buka && $event->konfirmasi_tutup) {
            $now = now()->timezone('Asia/Jakarta');
            $buka = \Carbon\Carbon::parse($event->konfirmasi_buka, 'Asia/Jakarta');
            $tutup = \Carbon\Carbon::parse($event->konfirmasi_tutup, 'Asia/Jakarta');

            if ($now->lt($buka) || $now->gt($tutup)) {
                return view('auth.konfirmasi-closed', [
                    'event' => $event,
                    'buka' => $buka,
                    'tutup' => $tutup,
                    'now' => $now,
                ]);
            }
        }

        return view('auth.konfirmasi');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'lembaga' => 'required|string|max:255',
            'domisili' => 'required|string|max:255',
            'nohp' => 'required|string|max:20',
            'menginap' => 'required|in:ya,tidak',
            'agreement' => 'required|accepted',
            'bukti_undangan' => 'required|file|mimes:pdf,jpg,jpeg,png|max:1024',
        ], [
            'nama.required' => 'Nama harus diisi',
            'lembaga.required' => 'Lembaga harus diisi',
            'domisili.required' => 'Domisili harus diisi',
            'nohp.required' => 'Nomor WhatsApp harus diisi',
            'menginap.required' => 'Pilihan menginap harus diisi',
            'agreement.required' => 'Anda harus menyetujui agreement',
            'agreement.accepted' => 'Anda harus menyetujui agreement',
            'bukti_undangan.required' => 'Bukti undangan harus diupload',
            'bukti_undangan.mimes' => 'Bukti undangan harus format PDF, JPG, atau PNG',
            'bukti_undangan.max' => 'Ukuran bukti undangan maksimal 1MB',
        ]);

        $nohp = $this->formatPhone($request->nohp);
        
        $existingUser = User::where('nohp', $nohp)->first();
        if ($existingUser) {
            return redirect()->back()->with('error', 'Nomor WhatsApp sudah terdaftar. Silakan login langsung.')->withInput();
        }

        try {
            $buktiUndanganPath = null;
            if ($request->hasFile('bukti_undangan')) {
                $file = $request->file('bukti_undangan');
                $extension = $file->getClientOriginalExtension();
                $filename = $nohp . '.' . $extension;
                
                $path = 'bukti_undangan/' . $filename;
                Storage::disk('public')->put($path, file_get_contents($file));
                $buktiUndanganPath = $path;
            }

            $user = User::create([
                'nama' => $request->nama,
                'lembaga' => $request->lembaga ?: 'PRIBADI',
                'domisili' => $request->domisili,
                'nohp' => $nohp,
                'password' => '',
                'menginap' => $request->menginap,
                'agreement_accepted_at' => now(),
                'role' => 'user',
                'bukti_undangan' => $buktiUndanganPath,
            ]);

            Session::put('user_id', $user->id);
            Session::put('role', $user->role);
            Session::put('nama', $user->nama);
            Session::put('nohp', $user->nohp);

            return redirect()->route('user.dashboard')->with('success', 'Pendaftaran berhasil! Selamat datang di Daurah Syariyyah.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    private function formatPhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (substr($phone, 0, 3) === '620') {
            return '0' . substr($phone, 3);
        }

        if (substr($phone, 0, 2) === '62') {
            return '0' . substr($phone, 2);
        }

        if (substr($phone, 0, 1) === '0') {
            return $phone;
        }

        return '0' . $phone;
    }
}