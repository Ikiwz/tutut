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
        Schema::table('questions', function (Blueprint $table) {
            $table->string('option_a_audio')->nullable()->after('option_a');
            $table->string('option_b_audio')->nullable()->after('option_b');
            $table->string('option_c_audio')->nullable()->after('option_c');
            $table->string('option_d_audio')->nullable()->after('option_d');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['option_a_audio', 'option_b_audio', 'option_c_audio', 'option_d_audio']);
        });
    }
};
