<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->foreignId('direction_id')->nullable()->after('section_id')
                ->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('direction_id');
        });
    }
};
