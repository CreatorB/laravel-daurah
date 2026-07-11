<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nohp' => 'required|string|max:20',
        ], [
            'nohp.required' => 'Nomor WhatsApp harus diisi',
        ]);

        $nohp = $this->formatPhone($request->nohp);
        
        $user = User::where('nohp', $nohp)->first();
        
        if ($user) {
            Session::put('user_id', $user->id);
            Session::put('role', $user->role);
            Session::put('nama', $user->nama);
            Session::put('nohp', $user->nohp);
            
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            
            return redirect()->route('user.dashboard');
        }
        
        return redirect()->route('login')
            ->withInput()
            ->with('error', 'Nomor WhatsApp belum terdaftar. Silakan daftar terlebih dahulu atau periksa kembali nomor Anda.');
    }

    public function logout()
    {
        Session::flush();
        return redirect()->route('login');
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
