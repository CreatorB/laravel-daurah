<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nohp', 20);
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->text('alamat')->nullable();
            $table->string('lembaga')->nullable();
            $table->enum('role', ['user', 'admin'])->default('user');
            $table->string('domisili')->nullable();
            $table->boolean('menginap')->default(false);
            $table->timestamp('agreement_accepted_at')->nullable();
            $table->string('bukti_undangan')->nullable();
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('nama_event');
            $table->date('tanggal');
            $table->enum('qr_mode', ['single', 'per_session'])->default('single');
            $table->boolean('cert_enabled')->default(false);
            $table->string('cert_template')->nullable();
            $table->string('cert_font')->nullable();
            $table->integer('cert_font_size')->nullable();
            $table->string('cert_font_color')->nullable();
            $table->decimal('radius_lat', 10, 8)->nullable();
            $table->decimal('radius_lng', 11, 8)->nullable();
            $table->boolean('radius_active')->default(false);
            $table->integer('radius_meters')->nullable();
            $table->string('group_link')->nullable();
            $table->enum('material_type', ['video', 'file', 'none'])->default('none');
            $table->boolean('auto_confirm')->default(false);
            $table->boolean('auto_invite')->default(false);
        });

        Schema::create('event_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('nama_sesi');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->timestamps();
        });

        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'confirmed', 'rejected'])->default('pending');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('session_id')->nullable()->constrained('event_sessions')->onDelete('set null');
            $table->enum('status', ['hadir', 'tidak_hadir'])->nullable();
            $table->timestamp('waktu_absen')->nullable();
            $table->enum('check_in_method', ['qr', 'manual'])->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamps();
        });

        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('nama_materi');
            $table->enum('tipe', ['video', 'file'])->nullable();
            $table->string('file_path')->nullable();
            $table->string('video_url')->nullable();
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        Schema::create('history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action');
            $table->string('module');
            $table->unsignedBigInteger('record_id')->nullable();
            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('history');
        Schema::dropIfExists('materials');
        Schema::dropIfExists('attendance');
        Schema::dropIfExists('event_registrations');
        Schema::dropIfExists('event_sessions');
        Schema::dropIfExists('events');
        Schema::dropIfExists('users');
    }
};
