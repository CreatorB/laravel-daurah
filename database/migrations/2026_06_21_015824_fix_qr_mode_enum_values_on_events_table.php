<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE events MODIFY qr_mode ENUM('static', 'dynamic') NOT NULL DEFAULT 'static'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE events MODIFY qr_mode ENUM('single', 'per_session') NOT NULL DEFAULT 'single'");
    }
};
