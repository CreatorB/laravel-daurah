<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MaintenanceController extends Controller
{
    public function index()
    {
        return view('admin.maintenance.index');
    }

    public function clearCache(Request $request)
    {
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        return redirect()->back()->with('success', 'Cache cleared successfully.');
    }

    public function clearView(Request $request)
    {
        Artisan::call('view:clear');
        return redirect()->back()->with('success', 'View cache cleared successfully.');
    }

    public function optimize(Request $request)
    {
        Artisan::call('config:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');
        return redirect()->back()->with('success', 'Application optimized (config, routes, views cached).');
    }

    public function clearAll(Request $request)
    {
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        return redirect()->back()->with('success', 'All caches cleared.');
    }

    public function dbStatus()
    {
        $tables = DB::select('SHOW TABLES');
        $tableNames = array_map(fn($row) => array_values((array)$row)[0], $tables);

        $tableInfo = [];
        foreach ($tableNames as $table) {
            $count = DB::table($table)->count();
            $tableInfo[] = [
                'name' => $table,
                'rows' => $count
            ];
        }

        return view('admin.maintenance.db-status', compact('tableInfo'));
    }

    public function migrate()
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
            $output = Artisan::output();
            return redirect()->back()->with('success', 'Migrations executed successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Migration failed: ' . $e->getMessage());
        }
    }

    public function storageLink()
    {
        try {
            Artisan::call('storage:link', ['--force' => true]);
            return redirect()->back()->with('success', 'Storage symlink created/verified.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Storage link failed: ' . $e->getMessage());
        }
    }

    public function resetDb()
    {
        try {
            Schema::disableForeignKeyConstraints();

            $tables = DB::select('SHOW TABLES');
            foreach ($tables as $table) {
                $tableName = array_values((array)$table)[0];
                Schema::dropIfExists($tableName);
            }

            Schema::enableForeignKeyConstraints();

            $migrateOutput = [];
            $migrateExitCode = 0;

            $phpBin = PHP_BINARY;
            $artisanPath = base_path('artisan');

            exec("\"$phpBin\" \"$artisanPath\" migrate --force 2>&1", $migrateOutput, $migrateExitCode);
            $migrateOutput = implode("\n", $migrateOutput);

            if ($migrateExitCode !== 0) {
                return redirect()->route('admin.maintenance.db-status')->with('error', 'Migration failed with exit code: ' . $migrateExitCode . '. Output: ' . $migrateOutput);
            }

            exec("\"$phpBin\" \"$artisanPath\" db:seed --class=AdminUserSeeder --force 2>&1", $seedOutput, $seedExitCode);
            $seedOutput = implode("\n", $seedOutput);

            if ($seedExitCode !== 0) {
                return redirect()->route('admin.maintenance.db-status')->with('error', 'Seeding failed: ' . $seedOutput);
            }

            $this->deleteUploadedFiles();

            return redirect()->route('admin.dashboard')->with('success', 'Database reset successfully. Admin: 089619060672');
        } catch (\Exception $e) {
            Schema::enableForeignKeyConstraints();
            return redirect()->back()->with('error', 'Reset failed: ' . $e->getMessage());
        }
    }

    public function seedAdmin()
    {
        try {
            User::updateOrCreate(
                ['nohp' => '089619060672'],
                [
                    'nama' => 'Admin Daurah',
                    'nohp' => '089619060672',
                    'email' => 'admin@daurah.syathiby.id',
                    'password' => null,
                    'alamat' => 'Jakarta',
                    'lembaga' => 'Daurah Syariyyah',
                    'role' => 'admin',
                    'domisili' => 'Jakarta',
                    'menginap' => 'tidak',
                    'agreement_accepted_at' => now(),
                ]
            );

            $adminCount = User::where('role', 'admin')->count();
            return redirect()->back()->with('success', "Admin account created/updated. Total admins: $adminCount");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Seeding failed: ' . $e->getMessage());
        }
    }

    private function deleteUploadedFiles()
    {
        $directories = [
            storage_path('app/public/bukti_undangan'),
            storage_path('app/public/certificates'),
            storage_path('app/public/materials'),
        ];

        foreach ($directories as $dir) {
            if (is_dir($dir)) {
                $files = glob($dir . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
            }
        }

        if (is_dir(public_path('storage'))) {
            $links = glob(public_path('storage') . '/*');
            foreach ($links as $link) {
                if (is_link($link) || is_file($link)) {
                    unlink($link);
                }
            }
        }
    }
}
