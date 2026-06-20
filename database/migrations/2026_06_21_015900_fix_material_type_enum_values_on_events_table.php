<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE events MODIFY material_type ENUM('per_session', 'once', 'none') NOT NULL DEFAULT 'none'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE events MODIFY material_type ENUM('video', 'file', 'none') NOT NULL DEFAULT 'none'");
    }
};
