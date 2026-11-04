<?php

use App\Models\ExamAttempt;
use App\Models\ExamIntegrityLog;
use App\Models\Section;
use App\Models\TestSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->student = User::create([
        'name' => 'Integrity Candidate',
        'email' => 'candidate@university.edu',
        'password' => bcrypt('password'),
        'role' => 'student',
        'email_verified_at' => now(),
    ]);

    $this->session = TestSession::create([
        'title' => 'Integrity Audit Test Session',
        'is_active' => true,
    ]);

    $this->section = Section::create([
        'name' => 'Structure & Written Expression',
        'slug' => 'structure',
        'duration_minutes' => 25,
        'order' => 1,
    ]);
    $this->session->sections()->attach($this->section->id);

    $this->attempt = ExamAttempt::create([
        'user_id' => $this->student->id,
        'test_session_id' => $this->session->id,
        'current_section_slug' => 'structure',
        'status' => 'in_progress',
        'started_at' => now(),
    ]);
});

test('candidate can record minor, medium, and severe integrity infractions', function () {
    $this->actingAs($this->student);

    // 1. Minor violation (contextmenu)
    $response1 = $this->postJson(route('exam.integrity.log', $this->attempt), [
        'event_type' => 'contextmenu',
        'severity' => 'minor',
        'title' => 'Right-Click Context Menu Attempt',
        'description' => 'Candidate attempted to right click.',
        'section_slug' => 'structure',
        'question_number' => 5,
    ]);

    $response1->assertOk()
        ->assertJson(['success' => true]);

    // 2. Medium violation (tab_switch)
    $response2 = $this->postJson(route('exam.integrity.log', $this->attempt), [
        'event_type' => 'tab_switch',
        'severity' => 'medium',
        'title' => 'Tab Switch Detected',
        'description' => 'Candidate switched away from exam tab.',
        'section_slug' => 'structure',
        'question_number' => 5,
    ]);

    $response2->assertOk()
        ->assertJson(['success' => true]);

    // 3. Severe violation (fullscreen_exit)
    $response3 = $this->postJson(route('exam.integrity.log', $this->attempt), [
        'event_type' => 'fullscreen_exit',
        'severity' => 'severe',
        'title' => 'Fullscreen Mode Exited',
        'description' => 'Candidate exited kiosk mode.',
        'section_slug' => 'structure',
    ]);

    $response3->assertOk()
        ->assertJson(['success' => true]);

    // Verify database records
    expect(ExamIntegrityLog::count())->toBe(3);

    $this->attempt->refresh();
    expect($this->attempt->focus_violations)->toBe(1)
        ->and($this->attempt->kiosk_violations)->toBe(1);
});

test('cannot log violation for another users attempt or completed attempt', function () {
    $otherUser = User::create([
        'name' => 'Impostor',
        'email' => 'impostor@university.edu',
        'password' => bcrypt('password'),
        'role' => 'student',
        'email_verified_at' => now(),
    ]);

    // Unauthorized user
    $this->actingAs($otherUser);
    $response = $this->postJson(route('exam.integrity.log', $this->attempt), [
        'event_type' => 'tab_switch',
        'severity' => 'medium',
        'title' => 'Tab Switch',
    ]);
    $response->assertForbidden();

    // Completed attempt
    $this->actingAs($this->student);
    $this->attempt->update(['status' => 'completed']);

    $responseCompleted = $this->postJson(route('exam.integrity.log', $this->attempt), [
        'event_type' => 'tab_switch',
        'severity' => 'medium',
        'title' => 'Tab Switch',
    ]);
    $responseCompleted->assertStatus(409);
});

test('result page displays recorded integrity violations audit report', function () {
    // Record violations on the attempt
    $this->attempt->logViolation(
        eventType: 'contextmenu',
        severity: 'minor',
        title: 'Right-Click Context Menu Attempt',
        description: 'Candidate attempted to right click on question text.',
        sectionSlug: 'structure',
        questionNumber: 3
    );

    $this->attempt->logViolation(
        eventType: 'tab_switch',
        severity: 'medium',
        title: 'Tab Switch Detected',
        description: 'Candidate switched tabs away from examination window.',
        sectionSlug: 'structure',
        questionNumber: 4
    );

    $this->attempt->update(['status' => 'completed']);

    $this->actingAs($this->student);
    $response = $this->get(route('exam.result', $this->attempt));

    $response->assertOk()
        ->assertSee('Session Integrity &amp; Activity Audit', false)
        ->assertSee('1 Medium (Sedang)')
        ->assertSee('1 Minor (Sepele)')
        ->assertSee('Right-Click Context Menu Attempt')
        ->assertSee('Tab Switch Detected')
        ->assertSee('Q#3')
        ->assertSee('Q#4');
});

test('result page displays clean integrity badge when 0 infractions occurred', function () {
    $this->attempt->update(['status' => 'completed']);

    $this->actingAs($this->student);
    $response = $this->get(route('exam.result', $this->attempt));

    $response->assertOk()
        ->assertSee('Session Integrity &amp; Activity Audit', false)
        ->assertSee('Flawless &bull; 0 Infractions', false)
        ->assertSee('Exemplary Academic Honesty');
});

test('admin attempt details page displays full proctoring integrity audit log', function () {
    $admin = User::create([
        'name' => 'Chief Proctor',
        'email' => 'proctor@university.edu',
        'password' => bcrypt('password'),
        'role' => 'admin',
    ]);

    $this->attempt->logViolation(
        eventType: 'devtools_key',
        severity: 'minor',
        title: 'Developer Tools Key (F12)',
        description: 'Candidate pressed F12 developer console key.',
        sectionSlug: 'structure',
        questionNumber: 8
    );

    $this->actingAs($admin);
    $response = $this->get(route('admin.attempts.show', $this->attempt));

    $response->assertOk()
        ->assertSee('Proctoring Integrity Audit Log')
        ->assertSee('1 Total Infraction(s)')
        ->assertSee('Developer Tools Key (F12)')
        ->assertSee('Q#8');
});
