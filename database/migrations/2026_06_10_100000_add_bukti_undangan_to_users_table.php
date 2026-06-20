<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'bukti_undangan')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('bukti_undangan', 255)->nullable()->after('menginap');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'bukti_undangan')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('bukti_undangan');
            });
        }
    }
};