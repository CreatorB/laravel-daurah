<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MaintenanceController extends Controller
{
    private function authenticate(Request $request): bool
    {
        $password = env('MAINTENANCE_PASSWORD', env('APP_KEY', ''));

        if (empty($password)) {
            return false;
        }

        $provided = $request->header('X-Maintenance-Password')
            ?? $request->query('key')
            ?? $request->input('key');

        return hash_equals($password, $provided);
    }

    private function unauthorized(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized. Provide X-Maintenance-Password header or ?key= parameter.'
        ], 401);
    }

    private function success(string $message, array $data = []): \Illuminate\Http\JsonResponse
    {
        return response()->json(array_merge([
            'status' => 'success',
            'message' => $message
        ], $data));
    }

    public function clearConfig(Request $request)
    {
        if (!$this->authenticate($request)) {
            return $this->unauthorized();
        }

        Artisan::call('config:clear');
        return $this->success('Configuration cache cleared.');
    }

    public function clearView(Request $request)
    {
        if (!$this->authenticate($request)) {
            return $this->unauthorized();
        }

        Artisan::call('view:clear');
        return $this->success('View cache cleared.');
    }

    public function optimize(Request $request)
    {
        if (!$this->authenticate($request)) {
            return $this->unauthorized();
        }

        Artisan::call('config:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');
        return $this->success('Application optimized (config, routes, views cached).');
    }

    public function clearAll(Request $request)
    {
        if (!$this->authenticate($request)) {
            return $this->unauthorized();
        }

        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        return $this->success('All caches cleared (config, view, cache, routes).');
    }

    public function migrate(Request $request)
    {
        if (!$this->authenticate($request)) {
            return $this->unauthorized();
        }

        try {
            Artisan::call('migrate', ['--force' => true]);
            $output = Artisan::output();

            return $this->success('Migrations executed successfully.', [
                'output' => $output
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Migration failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function migrateRollback(Request $request)
    {
        if (!$this->authenticate($request)) {
            return $this->unauthorized();
        }

        try {
            Artisan::call('migrate:rollback', ['--force' => true]);
            $output = Artisan::output();

            return $this->success('Migration rolled back.', [
                'output' => $output
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Rollback failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function migrateStatus(Request $request)
    {
        if (!$this->authenticate($request)) {
            return $this->unauthorized();
        }

        try {
            Artisan::call('migrate:status', ['--format' => 'json']);
            $output = Artisan::output();

            return $this->success('Migration status retrieved.', [
                'output' => json_decode($output, true) ?? $output
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Status check failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function dbStatus(Request $request)
    {
        if (!$this->authenticate($request)) {
            return $this->unauthorized();
        }

        try {
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

            return $this->success('Database status retrieved.', [
                'database' => config('database.connections.mysql.database'),
                'tables' => $tableInfo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database check failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storageLink(Request $request)
    {
        if (!$this->authenticate($request)) {
            return $this->unauthorized();
        }

        try {
            Artisan::call('storage:link', ['--force' => true]);
            $output = Artisan::output();

            return $this->success('Storage symlink created/verified.', [
                'output' => trim($output)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Storage link failed: ' . $e->getMessage()
            ], 500);
        }
    }
}