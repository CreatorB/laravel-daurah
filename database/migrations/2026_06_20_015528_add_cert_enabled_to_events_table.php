<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('events', 'cert_enabled')) {
            Schema::table('events', function (Blueprint $table) {
                $table->boolean('cert_enabled')->default(false)->after('qr_mode');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('events', 'cert_enabled')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn('cert_enabled');
            });
        }
    }
};
