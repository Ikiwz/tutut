<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exam_integrity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_attempt_id')->constrained()->cascadeOnDelete();
            $table->string('event_type', 50)->index();
            $table->string('severity', 20)->default('minor')->index();
            $table->string('title', 100);
            $table->string('description', 255)->nullable();
            $table->string('section_slug', 50)->nullable();
            $table->unsignedInteger('question_number')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('recorded_at')->useCurrent()->index();
            $table->timestamps();

            $table->index(['exam_attempt_id', 'severity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_integrity_logs');
    }
};
