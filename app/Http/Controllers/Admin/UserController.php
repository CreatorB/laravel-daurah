<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
public function index(Request $request)
    {
        $query = User::where('role', 'user');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('nohp', 'like', '%' . $search . '%')
                  ->orWhere('lembaga', 'like', '%' . $search . '%');
            });
        }

        if ($request->has('start_date') && $request->start_date) {
            $query->where('created_at', '>=', $request->start_date . ' 00:00:00');
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }

        $sortField = match($request->sort) {
            'nama' => ['field' => 'nama', 'direction' => 'asc'],
            'nama_desc' => ['field' => 'nama', 'direction' => 'desc'],
            'lembaga' => ['field' => 'lembaga', 'direction' => 'asc'],
            'lembaga_desc' => ['field' => 'lembaga', 'direction' => 'desc'],
            'created_at' => ['field' => 'created_at', 'direction' => 'asc'],
            'created_at_desc' => ['field' => 'created_at', 'direction' => 'desc'],
            default => ['field' => 'created_at', 'direction' => 'desc'],
        };
        
        $users = $query->orderBy($sortField['field'], $sortField['direction'])->get();
        
        if ($request->has('export_csv') || $request->has('export_excel')) {
            return $request->has('export_csv') 
                ? $this->exportFilteredCsv($users) 
                : $this->exportFilteredExcel($users);
        }

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nohp' => 'required|string|max:20|unique:users,nohp',
            'email' => 'nullable|email',
            'lembaga' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
        ]);

        $nohp = $this->formatPhone($request->nohp);

        User::create([
            'nama' => $request->nama,
            'nohp' => $nohp,
            'email' => $request->email,
            'lembaga' => $request->lembaga,
            'alamat' => $request->alamat,
            'role' => 'user',
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'nohp' => 'required|string|max:20|unique:users,nohp,' . $id,
            'email' => 'nullable|email',
            'lembaga' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
        ]);

        $nohp = $this->formatPhone($request->nohp);

        $user->update([
            'nama' => $request->nama,
            'nohp' => $nohp,
            'email' => $request->email,
            'lembaga' => $request->lembaga,
            'alamat' => $request->alamat,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diupdate!');
    }

public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->bukti_undangan) {
            Storage::disk('public')->delete($user->bukti_undangan);
        }
        
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus!');
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        $imported = 0;
        $errors = [];

        fgetcsv($handle);

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 2) continue;

            $nama = trim($row[0]);
            $nohp = trim($row[1]);
            $lembaga = isset($row[2]) ? trim($row[2]) : 'PRIBADI';
            $alamat = isset($row[3]) ? trim($row[3]) : null;

            if (empty($nama) || empty($nohp)) continue;

            $nohp = $this->formatPhone($nohp);

            if (User::where('nohp', $nohp)->exists()) continue;

            try {
                User::create([
                    'nama' => $nama,
                    'nohp' => $nohp,
                    'lembaga' => $lembaga,
                    'alamat' => $alamat,
                    'role' => 'user',
                ]);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Gagal import: $nama";
            }
        }

        fclose($handle);

        $msg = "$imported user berhasil diimport!";
        if (count($errors) > 0) {
            $msg .= " Gagal: " . count($errors);
        }

        return redirect()->route('admin.users.index')->with('success', $msg);
    }

public function exportCsv(Request $request)
    {
        $query = User::where('role', 'user');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('nohp', 'like', '%' . $search . '%')
                  ->orWhere('lembaga', 'like', '%' . $search . '%');
            });
        }

        if ($request->has('start_date') && $request->start_date) {
            $query->where('created_at', '>=', $request->start_date . ' 00:00:00');
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }

        $users = $query->orderBy('nama')->get();

        return $this->exportFilteredCsv($users);
    }

    public function exportExcel(Request $request)
    {
        $query = User::where('role', 'user');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('nohp', 'like', '%' . $search . '%')
                  ->orWhere('lembaga', 'like', '%' . $search . '%');
            });
        }

        if ($request->has('start_date') && $request->start_date) {
            $query->where('created_at', '>=', $request->start_date . ' 00:00:00');
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }

        $users = $query->orderBy('nama')->get();

        return $this->exportFilteredExcel($users);
    }

    private function exportFilteredCsv($users)
    {
        $filename = 'users_' . date('Ymd_His') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['No', 'Nama', 'No HP', 'Email', 'Lembaga', 'Alamat', 'Domisili', 'Menginap', 'Tanggal Daftar']);
        
        $no = 1;
        foreach ($users as $user) {
            fputcsv($output, [
                $no++,
                $user->nama,
                '+' . $user->nohp,
                $user->email ?? '-',
                $user->lembaga,
                $user->alamat ?? '-',
                $user->domisili ?? '-',
                $user->menginap == 'ya' ? 'Ya' : 'Tidak',
                $user->created_at->format('Y-m-d H:i:s'),
            ]);
        }
        
        fclose($output);
        exit;
    }

    private function exportFilteredExcel($users)
    {
        $filename = 'users_' . date('Ymd_His') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
        
        fputcsv($output, ['No', 'Nama', 'No HP', 'Email', 'Lembaga', 'Alamat', 'Domisili', 'Menginap', 'Tanggal Daftar']);
        
        $no = 1;
        foreach ($users as $user) {
            fputcsv($output, [
                $no++,
                $user->nama,
                '+' . $user->nohp,
                $user->email ?? '-',
                $user->lembaga,
                $user->alamat ?? '-',
                $user->domisili ?? '-',
                $user->menginap == 'ya' ? 'Ya' : 'Tidak',
                $user->created_at->format('Y-m-d H:i:s'),
            ]);
        }
        
        fclose($output);
        exit;
    }

    private function formatPhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (substr($phone, 0, 2) == '08') {
            return '62' . substr($phone, 1);
        }
        
        if (substr($phone, 0, 3) == '+62') {
            return substr($phone, 1);
        }
        
        if (substr($phone, 0, 2) == '62') {
            return $phone;
        }
        
        return $phone;
    }
}
