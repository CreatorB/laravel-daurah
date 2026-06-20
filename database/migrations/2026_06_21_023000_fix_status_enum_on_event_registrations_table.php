<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE event_registrations MODIFY status ENUM('pending', 'confirmed', 'declined') NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE event_registrations MODIFY status ENUM('pending', 'confirmed', 'rejected') NULL DEFAULT 'pending'");
    }
};
