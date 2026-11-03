<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'affiliation', 'campus_claim_status', 'campus_claim_notes', 'student_id', 'department', 'student_card', 'verified_at', 'email_verified_at', 'google_id', 'avatar'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected $attributes = [
        'role' => 'student',
        'affiliation' => 'internal',
        'student_id' => null,
        'department' => null,
        'student_card' => null,
        'campus_claim_status' => null,
        'email_verified_at' => null,
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student' || $this->role === 'candidate';
    }

    public function isCandidate(): bool
    {
        return $this->isStudent();
    }

    public function isInternal(): bool
    {
        return $this->affiliation === 'internal' || str_ends_with(strtolower($this->email), '@ummi.ac.id');
    }

    public function isExternal(): bool
    {
        return ! $this->isInternal();
    }

    public function completedAttemptsCount(): int
    {
        return $this->examAttempts()->where('status', 'completed')->count();
    }

    public function hasFreeAttemptAvailable(): bool
    {
        return $this->isInternal() && $this->completedAttemptsCount() === 0;
    }

    public function isEligibleForTestSession(TestSession $session): array
    {
        // 1. Internal candidate with 0 completed attempts gets 1 free attempt
        if ($this->hasFreeAttemptAvailable()) {
            return [
                'eligible' => true,
                'reason' => 'Complimentary university attempt available.',
                'type' => 'free',
            ];
        }

        // 2. Check for approved payment
        $hasApprovedPayment = $this->payments()
            ->where('status', 'approved')
            ->where(function ($q) use ($session) {
                $q->whereNull('test_session_id')
                    ->orWhere('test_session_id', $session->id);
            })
            ->exists();

        if ($hasApprovedPayment) {
            return [
                'eligible' => true,
                'reason' => 'Registration payment verified.',
                'type' => 'paid',
            ];
        }

        // 3. Check for pending payment or claim
        $hasPendingPayment = $this->payments()->where('status', 'pending')->exists();
        if ($hasPendingPayment) {
            return [
                'eligible' => false,
                'reason' => 'Your payment receipt is currently under administrative review.',
                'type' => 'pending_review',
            ];
        }

        if ($this->campus_claim_status === 'pending') {
            return [
                'eligible' => false,
                'reason' => 'Your campus affiliation claim is currently under administrative review.',
                'type' => 'claim_pending',
            ];
        }

        return [
            'eligible' => false,
            'reason' => $this->isInternal()
                ? 'Your complimentary university attempt has been used. Retakes require registration payment.'
                : 'External candidates require registration payment before taking the examination.',
            'type' => 'payment_required',
        ];
    }

    public function examAttempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ExamPayment::class);
    }
}
