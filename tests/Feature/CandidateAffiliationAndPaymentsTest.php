<?php

use App\Models\ExamAttempt;
use App\Models\ExamPayment;
use App\Models\Section;
use App\Models\TestSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('candidate registering with @ummi.ac.id email is auto-classified as internal and verified', function () {
    $response = $this->post(route('student.register.submit'), [
        'name' => 'Fauzul UMMI',
        'email' => 'fauzul@ummi.ac.id',
        'password' => 'SecurePass123!',
        'password_confirmation' => 'SecurePass123!',
    ]);

    $response->assertRedirect(route('verification.notice'));

    $user = User::where('email', 'fauzul@ummi.ac.id')->first();
    expect($user)->not->toBeNull()
        ->and($user->affiliation)->toBe('internal')
        ->and($user->verified_at)->not->toBeNull()
        ->and($user->isInternal())->toBeTrue();
});

test('candidate registering with external email is classified as external', function () {
    $response = $this->post(route('student.register.submit'), [
        'name' => 'External Candidate',
        'email' => 'candidate@gmail.com',
        'password' => 'SecurePass123!',
        'password_confirmation' => 'SecurePass123!',
    ]);

    $response->assertRedirect(route('verification.notice'));

    $user = User::where('email', 'candidate@gmail.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->affiliation)->toBe('external')
        ->and($user->verified_at)->toBeNull()
        ->and($user->isInternal())->toBeFalse();
});

test('internal candidate with 0 completed attempts is eligible for free attempt', function () {
    $candidate = User::factory()->create([
        'affiliation' => 'internal',
    ]);

    $session = TestSession::create([
        'title' => 'Complimentary TOEFL',
        'is_active' => true,
    ]);

    expect($candidate->hasFreeAttemptAvailable())->toBeTrue();

    $eligibility = $candidate->isEligibleForTestSession($session);
    expect($eligibility['eligible'])->toBeTrue()
        ->and($eligibility['type'])->toBe('free');
});

test('internal candidate with completed attempt requires payment for retake', function () {
    $candidate = User::factory()->create([
        'affiliation' => 'internal',
    ]);

    $session = TestSession::create([
        'title' => 'Retake TOEFL Session',
        'is_active' => true,
    ]);

    ExamAttempt::create([
        'user_id' => $candidate->id,
        'test_session_id' => $session->id,
        'status' => 'completed',
        'completed_at' => now(),
    ]);

    expect($candidate->hasFreeAttemptAvailable())->toBeFalse();

    $eligibility = $candidate->isEligibleForTestSession($session);
    expect($eligibility['eligible'])->toBeFalse()
        ->and($eligibility['type'])->toBe('payment_required');
});

test('external candidate can submit a campus status claim with NIM and KTM document', function () {
    Storage::fake('local');

    $candidate = User::factory()->external()->create([
        'name' => 'John External',
        'email' => 'john.external@gmail.com',
    ]);

    $file = UploadedFile::fake()->create('ktm.jpg', 200, 'image/jpeg');

    $response = $this->actingAs($candidate)->post(route('student.claim-campus'), [
        'student_id' => '2230511999',
        'department' => 'Teknik Informatika',
        'student_card' => $file,
    ]);

    $response->assertRedirect(route('student.dashboard'));
    $response->assertSessionHas('success');

    $candidate->refresh();
    expect($candidate->student_id)->toBe('2230511999')
        ->and($candidate->department)->toBe('Teknik Informatika')
        ->and($candidate->campus_claim_status)->toBe('pending');

    Storage::disk('local')->assertExists($candidate->student_card);
});

test('admin can approve campus claim making candidate internal with free attempt', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $candidate = User::factory()->external()->create([
        'student_id' => '2230511888',
        'department' => 'Sistem Informasi',
        'campus_claim_status' => 'pending',
    ]);

    $response = $this->actingAs($admin)->post(route('admin.students.approve-claim', $candidate));

    $response->assertRedirect();
    $candidate->refresh();

    expect($candidate->affiliation)->toBe('internal')
        ->and($candidate->campus_claim_status)->toBe('approved')
        ->and($candidate->hasFreeAttemptAvailable())->toBeTrue();
});

test('candidate can submit payment proof and admin can approve it', function () {
    Storage::fake('local');

    $admin = User::factory()->create(['role' => 'admin']);
    $candidate = User::factory()->external()->create();

    $session = TestSession::create([
        'title' => 'Public TOEFL Test',
        'is_active' => true,
    ]);

    $file = UploadedFile::fake()->create('transfer_receipt.png', 300, 'image/png');

    $response = $this->actingAs($candidate)->post(route('student.payment.submit'), [
        'test_session_id' => $session->id,
        'payment_method' => 'BSI Transfer',
        'amount' => 100000,
        'proof' => $file,
    ]);

    $response->assertRedirect(route('student.dashboard'));

    $payment = ExamPayment::where('user_id', $candidate->id)->first();
    expect($payment)->not->toBeNull()
        ->and($payment->status)->toBe('pending');

    // Admin approves payment
    $approveResponse = $this->actingAs($admin)->post(route('admin.payments.approve', $payment));
    $approveResponse->assertRedirect();

    $payment->refresh();
    expect($payment->isApproved())->toBeTrue()
        ->and($payment->verified_by)->toBe($admin->id);

    // Now candidate is eligible for the session
    $eligibility = $candidate->isEligibleForTestSession($session);
    expect($eligibility['eligible'])->toBeTrue()
        ->and($eligibility['type'])->toBe('paid');
});

test('test session capacity blocks exam start when full', function () {
    $candidate = User::factory()->create(['affiliation' => 'internal']);

    $session = TestSession::create([
        'title' => 'Limited Capacity Session',
        'is_active' => true,
        'capacity' => 1,
    ]);

    $section = Section::create([
        'name' => 'Listening Comprehension',
        'slug' => 'listening',
        'duration_minutes' => 35,
        'order' => 1,
    ]);
    $session->sections()->attach($section->id);

    // 1 seat already taken
    $otherCandidate = User::factory()->create();
    ExamAttempt::create([
        'user_id' => $otherCandidate->id,
        'test_session_id' => $session->id,
        'status' => 'in_progress',
    ]);

    expect($session->isFull())->toBeTrue();

    // Trying to start should be redirected with capacity error
    $response = $this->actingAs($candidate)->post(route('exam.start', $session));

    $response->assertRedirect(route('student.dashboard'));
    $response->assertSessionHas('error');
});
