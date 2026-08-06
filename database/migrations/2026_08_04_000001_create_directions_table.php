<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('directions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained()->cascadeOnDelete();
            $table->string('label');          // e.g. "Part A", "Part B", "Directions"
            $table->string('title');          // e.g. "Short Conversations"
            $table->text('description')->nullable(); // Detailed instruction text
            $table->string('audio_path')->nullable(); // Optional audio file
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('directions');
    }
};
