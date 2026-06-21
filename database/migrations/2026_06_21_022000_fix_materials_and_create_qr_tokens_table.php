<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('materials', 'deskripsi')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->text('deskripsi')->nullable()->after('nama_materi');
            });
        }

        foreach (['tipe', 'file_path', 'video_url', 'urutan'] as $legacyColumn) {
            if (Schema::hasColumn('materials', $legacyColumn)) {
                Schema::table('materials', function (Blueprint $table) use ($legacyColumn) {
                    $table->dropColumn($legacyColumn);
                });
            }
        }

        if (!Schema::hasTable('qr_tokens')) {
            Schema::create('qr_tokens', function (Blueprint $table) {
                $table->id();
                $table->string('token', 64)->nullable();
                $table->unsignedBigInteger('event_id')->nullable();
                $table->dateTime('created_at')->useCurrent();
                $table->dateTime('expires_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_tokens');

        if (Schema::hasColumn('materials', 'deskripsi')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->dropColumn('deskripsi');
            });
        }
    }
};
