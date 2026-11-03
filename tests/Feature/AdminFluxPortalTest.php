<?php

use App\Models\Section;
use App\Models\TestSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest accessing admin dashboard is redirected to admin login', function () {
    $response = $this->get('/admin');

    $response->assertRedirect(route('admin.login'));
});

test('student cannot access admin dashboard', function () {
    $student = User::create([
        'name' => 'Student One',
        'email' => 'student@toefl.com',
        'password' => 'secret123',
        'role' => 'student',
    ]);

    $response = $this->actingAs($student)->get('/admin');

    $response->assertRedirect(route('admin.login'));
});

test('admin can view admin login page and successfully log in', function () {
    $admin = User::create([
        'name' => 'Campus Admin',
        'email' => 'admin@toefl.com',
        'password' => 'secret123',
        'role' => 'admin',
    ]);

    $response = $this->get(route('admin.login'));
    $response->assertOk()
        ->assertSee('TUTUT Admin Portal');

    $loginResponse = $this->post(route('admin.login.submit'), [
        'email' => 'admin@toefl.com',
        'password' => 'secret123',
    ]);

    $loginResponse->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticatedAs($admin);
});

test('admin can access dashboard and view official flux tables', function () {
    $admin = User::create([
        'name' => 'Campus Admin',
        'email' => 'admin@toefl.com',
        'password' => 'secret123',
        'role' => 'admin',
    ]);

    $section = Section::create([
        'name' => 'Listening Comprehension',
        'slug' => 'listening',
        'duration_minutes' => 35,
        'order' => 1,
    ]);

    $session = TestSession::create([
        'title' => 'TOEFL Test Batch 1',
        'access_code' => 'BATCH-01',
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin)->get(route('admin.dashboard'));
    $response->assertOk()
        ->assertSee('Dashboard Overview')
        ->assertSee('Total Students')
        ->assertSee('Total Questions')
        ->assertSee('Active Sessions');

    $sessionsResponse = $this->actingAs($admin)->get(route('admin.test-sessions.index'));
    $sessionsResponse->assertOk()
        ->assertSee('Test Sessions')
        ->assertSee('TOEFL Test Batch 1')
        ->assertSee('BATCH-01');

    $sectionsResponse = $this->actingAs($admin)->get(route('admin.sections.index'));
    $sectionsResponse->assertOk()
        ->assertSee('Exam Sections')
        ->assertSee('Listening Comprehension');

    $questionsResponse = $this->actingAs($admin)->get(route('admin.questions.index'));
    $questionsResponse->assertOk()
        ->assertSee('Questions Vault');

    $attemptsResponse = $this->actingAs($admin)->get(route('admin.attempts.index'));
    $attemptsResponse->assertOk()
        ->assertSee('Exam Results &amp; Proctoring Audit', false);

    $studentsResponse = $this->actingAs($admin)->get(route('admin.students.index'));
    $studentsResponse->assertOk()
        ->assertSee('Student Academic Verification');
});

test('admin can toggle student academic verification status', function () {
    $admin = User::create([
        'name' => 'Campus Admin',
        'email' => 'admin@toefl.com',
        'password' => 'secret123',
        'role' => 'admin',
    ]);

    $student = User::create([
        'name' => 'Candidate A',
        'email' => 'candidate@toefl.com',
        'password' => 'secret123',
        'role' => 'student',
        'student_id' => 'STU-9901',
        'verified_at' => null,
    ]);

    $response = $this->actingAs($admin)->post(route('admin.students.verify', $student));
    $response->assertRedirect();

    $student->refresh();
    expect($student->verified_at)->not->toBeNull();

    // Revoke verification
    $this->actingAs($admin)->post(route('admin.students.verify', $student));
    $student->refresh();
    expect($student->verified_at)->toBeNull();
});
