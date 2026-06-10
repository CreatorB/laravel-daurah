<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class KonfirmasiController extends Controller
{
    public function showForm()
    {
        return view('auth.konfirmasi');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'lembaga' => 'nullable|string|max:255',
            'domisili' => 'required|string|max:255',
            'nohp' => 'required|string|max:20',
            'menginap' => 'required|in:ya,tidak',
            'agreement' => 'required|accepted',
        ], [
            'nama.required' => 'Nama harus diisi',
            'domisili.required' => 'Domisili harus diisi',
            'nohp.required' => 'Nomor WhatsApp harus diisi',
            'menginap.required' => 'Pilihan menginap harus diisi',
            'agreement.required' => 'Anda harus menyetujui agreement',
            'agreement.accepted' => 'Anda harus menyetujui agreement',
        ]);

        $nohp = $this->formatPhone($request->nohp);
        
        $existingUser = User::where('nohp', $nohp)->first();
        if ($existingUser) {
            return redirect()->back()->with('error', 'Nomor WhatsApp sudah terdaftar. Silakan login langsung.');
        }

        try {
            $user = User::create([
                'nama' => $request->nama,
                'lembaga' => $request->lembaga ?: 'PRIBADI',
                'domisili' => $request->domisili,
                'nohp' => $nohp,
                'password' => '',
                'menginap' => $request->menginap,
                'agreement_accepted_at' => now(),
                'role' => 'user',
            ]);

            Session::put('user_id', $user->id);
            Session::put('role', $user->role);
            Session::put('nama', $user->nama);
            Session::put('nohp', $user->nohp);

            return redirect()->route('user.dashboard')->with('success', 'Pendaftaran berhasil! Selamat datang di Daurah Syariyyah.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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