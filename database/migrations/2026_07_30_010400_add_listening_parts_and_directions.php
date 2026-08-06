<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->string('part', 1)->nullable()->after('section_id')
                ->comment('Listening part: A, B, or C');
        });

        Schema::table('sections', function (Blueprint $table) {
            $table->string('directions_audio_a')->nullable()->after('description');
            $table->string('directions_audio_b')->nullable()->after('directions_audio_a');
            $table->string('directions_audio_c')->nullable()->after('directions_audio_b');
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('part');
        });

        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn(['directions_audio_a', 'directions_audio_b', 'directions_audio_c']);
        });
    }
};
