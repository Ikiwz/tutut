<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Academic Student Verification & Identity
        Schema::table('users', function (Blueprint $table) {
            $table->string('student_id', 20)->nullable()->unique()->after('role');
            $table->string('department', 100)->nullable()->after('student_id');
            $table->string('student_card', 255)->nullable()->after('department');
            $table->timestamp('verified_at')->nullable()->after('student_card');
            $table->softDeletes()->after('updated_at');
        });

        // 2. Proctoring Access Code & Lifecycle Protection
        Schema::table('test_sessions', function (Blueprint $table) {
            $table->string('access_code', 16)->nullable()->after('description');
            $table->softDeletes()->after('updated_at');
        });

        // 3. Domain Entity Lifecycle Protection (Soft Deletes)
        Schema::table('sections', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
        });

        Schema::table('passages', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
        });

        Schema::table('directions', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
        });

        // 4. High-Security Anti-Tamper & Proctoring Telemetry on Exam Attempts
        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->string('serial_number', 32)->nullable()->unique()->after('id');
            $table->string('device_token', 64)->nullable()->after('status');
            $table->string('device_fingerprint', 64)->nullable()->after('device_token');
            $table->string('ip_address', 45)->nullable()->after('device_fingerprint');
            $table->string('user_agent', 255)->nullable()->after('ip_address');
            $table->unsignedSmallInteger('focus_violations')->default(0)->after('user_agent');
            $table->unsignedSmallInteger('kiosk_violations')->default(0)->after('focus_violations');
            $table->char('signature', 64)->nullable()->after('kiosk_violations');
            $table->softDeletes()->after('updated_at');
        });

        // 5. Anti-Leak Answer Key Vault (Separation of Concerns for Question Keys)
        Schema::create('exam_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->unique()->constrained('questions')->cascadeOnDelete();
            $table->enum('correct_key', ['A', 'B', 'C', 'D']);
            $table->timestamps();
        });

        // 6. Smooth Data Backfill: Populate exam_keys from existing questions table
        if (Schema::hasTable('questions') && Schema::hasColumn('questions', 'correct_answer')) {
            $questions = DB::table('questions')
                ->whereIn('correct_answer', ['A', 'B', 'C', 'D'])
                ->get(['id', 'correct_answer']);

            $now = now();
            $records = $questions->map(fn ($q) => [
                'question_id' => $q->id,
                'correct_key' => $q->correct_answer,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all();

            if (! empty($records)) {
                DB::table('exam_keys')->insert($records);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop Vault Table First
        Schema::dropIfExists('exam_keys');

        // Reverse Exam Attempts Changes
        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropUnique(['serial_number']);
            $table->dropColumn([
                'serial_number',
                'device_token',
                'device_fingerprint',
                'ip_address',
                'user_agent',
                'focus_violations',
                'kiosk_violations',
                'signature',
            ]);
        });

        // Reverse Soft Deletes
        Schema::table('questions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('directions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('passages', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('sections', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        // Reverse Test Session Changes
        Schema::table('test_sessions', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn('access_code');
        });

        // Reverse Users Changes
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropUnique(['student_id']);
            $table->dropColumn([
                'student_id',
                'department',
                'student_card',
                'verified_at',
            ]);
        });
    }
};
