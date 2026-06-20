<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY menginap VARCHAR(10) NULL DEFAULT NULL");
        DB::statement("UPDATE users SET menginap = CASE WHEN menginap = '1' THEN 'ya' WHEN menginap = '0' THEN 'tidak' ELSE menginap END");
        DB::statement("ALTER TABLE users MODIFY menginap ENUM('ya', 'tidak') NULL DEFAULT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY menginap TINYINT(1) NOT NULL DEFAULT 0");
    }
};
