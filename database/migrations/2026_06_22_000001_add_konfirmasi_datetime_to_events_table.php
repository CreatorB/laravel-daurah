<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dateTime('konfirmasi_buka')->nullable()->after('auto_invite');
            $table->dateTime('konfirmasi_tutup')->nullable()->after('konfirmasi_buka');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['konfirmasi_buka', 'konfirmasi_tutup']);
        });
    }
};
