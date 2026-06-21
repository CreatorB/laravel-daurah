<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('attendance', 'waktu_absen') && !Schema::hasColumn('attendance', 'waktu_scan')) {
            Schema::table('attendance', function ($table) {
                $table->renameColumn('waktu_absen', 'waktu_scan');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('attendance', 'waktu_scan') && !Schema::hasColumn('attendance', 'waktu_absen')) {
            Schema::table('attendance', function ($table) {
                $table->renameColumn('waktu_scan', 'waktu_absen');
            });
        }
    }
};
