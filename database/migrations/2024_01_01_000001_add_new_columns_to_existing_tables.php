<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // users table - add new columns
        if (!Schema::hasColumn('users', 'domisili')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('domisili', 255)->nullable()->after('lembaga');
            });
        }
        
        if (!Schema::hasColumn('users', 'menginap')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('menginap', ['ya', 'tidak'])->nullable()->after('domisili');
            });
        }
        
        if (!Schema::hasColumn('users', 'agreement_accepted_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dateTime('agreement_accepted_at')->nullable()->after('menginap');
            });
        }
        
        // events table - add new columns
        if (!Schema::hasColumn('events', 'radius_lat')) {
            Schema::table('events', function (Blueprint $table) {
                $table->decimal('radius_lat', 10, 8)->nullable()->after('cert_font_color');
            });
        }
        
        if (!Schema::hasColumn('events', 'radius_lng')) {
            Schema::table('events', function (Blueprint $table) {
                $table->decimal('radius_lng', 11, 8)->nullable()->after('radius_lat');
            });
        }
        
        if (!Schema::hasColumn('events', 'radius_active')) {
            Schema::table('events', function (Blueprint $table) {
                $table->boolean('radius_active')->default(false)->after('radius_lng');
            });
        }
        
        if (!Schema::hasColumn('events', 'radius_meters')) {
            Schema::table('events', function (Blueprint $table) {
                $table->integer('radius_meters')->default(100)->after('radius_active');
            });
        }
        
        if (!Schema::hasColumn('events', 'group_link')) {
            Schema::table('events', function (Blueprint $table) {
                $table->string('group_link', 500)->nullable()->after('radius_meters');
            });
        }
        
        if (!Schema::hasColumn('events', 'material_type')) {
            Schema::table('events', function (Blueprint $table) {
                $table->enum('material_type', ['per_session', 'once', 'none'])->default('none')->after('group_link');
            });
        }
        
        if (!Schema::hasColumn('events', 'auto_confirm')) {
            Schema::table('events', function (Blueprint $table) {
                $table->boolean('auto_confirm')->default(false)->after('material_type');
            });
        }
        
        // event_sessions table - add material_id
        if (!Schema::hasColumn('event_sessions', 'material_id')) {
            Schema::table('event_sessions', function (Blueprint $table) {
                $table->integer('material_id')->nullable()->after('jam_selesai');
            });
        }
        
        // attendance table - add materi confirmation columns
        if (!Schema::hasColumn('attendance', 'materi_confirmed')) {
            Schema::table('attendance', function (Blueprint $table) {
                $table->boolean('materi_confirmed')->default(false)->after('session_id');
            });
        }
        
        if (!Schema::hasColumn('attendance', 'materi_confirmed_at')) {
            Schema::table('attendance', function (Blueprint $table) {
                $table->dateTime('materi_confirmed_at')->nullable()->after('materi_confirmed');
            });
        }
        
        // Create materials table (new) - without foreign key to avoid type mismatch
        if (!Schema::hasTable('materials')) {
            Schema::create('materials', function (Blueprint $table) {
                $table->id();
                $table->integer('event_id')->unsigned()->nullable();
                $table->string('nama_materi', 255);
                $table->text('deskripsi')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'agreement_accepted_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('agreement_accepted_at');
            });
        }
        
        Schema::dropIfExists('materials');
    }
};
