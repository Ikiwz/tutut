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
        // 1. User Affiliation & Campus Status Claim
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'affiliation')) {
                $table->string('affiliation', 20)->default('external')->after('role');
            }
            if (! Schema::hasColumn('users', 'campus_claim_status')) {
                $table->string('campus_claim_status', 20)->nullable()->after('affiliation');
            }
            if (! Schema::hasColumn('users', 'campus_claim_notes')) {
                $table->text('campus_claim_notes')->nullable()->after('campus_claim_status');
            }
        });

        // Auto-classify existing @ummi.ac.id accounts as internal
        DB::table('users')
            ->where('email', 'like', '%@ummi.ac.id')
            ->update([
                'affiliation' => 'internal',
                'verified_at' => DB::raw('COALESCE(verified_at, CURRENT_TIMESTAMP)'),
            ]);

        // 2. Test Session Capacity & Quota Management
        Schema::table('test_sessions', function (Blueprint $table) {
            if (! Schema::hasColumn('test_sessions', 'capacity')) {
                $table->unsignedSmallInteger('capacity')->nullable()->default(50)->after('access_code');
            }
        });

        // 3. Exam Payments & Verification Receipts
        if (! Schema::hasTable('exam_payments')) {
            Schema::create('exam_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('test_session_id')->nullable()->constrained('test_sessions')->nullOnDelete();
                $table->decimal('amount', 12, 2)->default(100000.00);
                $table->string('payment_method', 50)->default('bank_transfer');
                $table->string('proof_path', 255);
                $table->string('status', 20)->default('pending'); // pending, approved, rejected
                $table->text('rejection_reason')->nullable();
                $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('verified_at')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['user_id', 'status']);
                $table->index(['test_session_id', 'status']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_payments');

        Schema::table('test_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('test_sessions', 'capacity')) {
                $table->dropColumn('capacity');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('users', 'campus_claim_notes')) {
                $columns[] = 'campus_claim_notes';
            }
            if (Schema::hasColumn('users', 'campus_claim_status')) {
                $columns[] = 'campus_claim_status';
            }
            if (Schema::hasColumn('users', 'affiliation')) {
                $columns[] = 'affiliation';
            }
            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
