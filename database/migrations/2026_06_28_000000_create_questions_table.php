<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();

            $table->string('name')->nullable();
            $table->mediumText('question_body');
            $table->boolean('is_anonymous')->default(false);

            $table->string('locale', 5)->default('id');

            $table->string('status', 20)->default('pending')->index();

            $table->timestamp('approved_at')->nullable();
            $table->unsignedInteger('approved_by')->nullable();

            $table->timestamp('rejected_at')->nullable();
            $table->unsignedInteger('rejected_by')->nullable();

            $table->timestamp('published_at')->nullable()->index();

            $table->string('lesson_code')->nullable()->index();
            $table->string('public_ref', 20)->nullable()->unique();

            $table->text('admin_note')->nullable();

            $table->timestamps();

            $table->index(['status', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
