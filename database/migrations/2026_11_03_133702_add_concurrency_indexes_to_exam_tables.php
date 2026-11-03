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
        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'idx_exam_attempts_user_status');
            $table->index(['test_session_id', 'status'], 'idx_exam_attempts_session_status');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index(['role', 'affiliation'], 'idx_users_role_affiliation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_role_affiliation');
        });

        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->dropIndex('idx_exam_attempts_user_status');
            $table->dropIndex('idx_exam_attempts_session_status');
        });
    }
};
