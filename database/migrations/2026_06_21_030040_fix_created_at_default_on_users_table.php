<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP");
        DB::statement("UPDATE users SET created_at = NOW() WHERE created_at IS NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY created_at TIMESTAMP NULL DEFAULT NULL");
    }
};
