<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('test_session_id')->constrained()->cascadeOnDelete();
            $table->string('current_section_slug')->nullable();
            $table->integer('current_question_index')->default(0);
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->integer('score_listening')->nullable();
            $table->integer('score_structure')->nullable();
            $table->integer('score_reading')->nullable();
            $table->integer('total_score')->nullable();
            $table->string('status')->default('in_progress'); // in_progress, completed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_attempts');
    }
};
