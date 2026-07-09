<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recycle_bin', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type', 100)->index();
            $table->unsignedBigInteger('entity_id')->index();
            $table->string('label', 255)->nullable();
            $table->json('snapshot')->nullable();
            $table->string('deleted_by_name', 191)->nullable();
            $table->timestamp('deleted_at')->useCurrent();
            $table->timestamp('restored_at')->nullable();
            $table->string('restored_by_name', 191)->nullable();
            $table->timestamp('permanently_deleted_at')->nullable();
            $table->string('permanently_deleted_by_name', 191)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recycle_bin');
    }
};
