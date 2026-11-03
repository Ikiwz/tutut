<?php

use App\Models\ExamAnswer;
use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\Section;
use App\Models\TestSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

test('answering question on completed exam returns conflict error 409', function () {
    $candidate = User::factory()->create(['affiliation' => 'internal']);

    $session = TestSession::create([
        'title' => 'TOEFL Test',
        'is_active' => true,
    ]);

    $section = Section::create([
        'name' => 'Structure & Written Expression',
        'slug' => 'structure',
        'duration_minutes' => 25,
        'order' => 2,
    ]);
    $session->sections()->attach($section->id);

    $question = Question::create([
        'section_id' => $section->id,
        'question_text' => 'Sample Question',
        'option_a' => 'Choice A',
        'option_b' => 'Choice B',
        'option_c' => 'Choice C',
        'option_d' => 'Choice D',
        'correct_answer' => 'A',
    ]);

    $attempt = ExamAttempt::create([
        'user_id' => $candidate->id,
        'test_session_id' => $session->id,
        'status' => 'completed',
        'completed_at' => now(),
    ]);

    $response = $this->actingAs($candidate)->postJson(route('exam.answer', $attempt), [
        'question_id' => $question->id,
        'selected_answer' => 'A',
    ]);

    $response->assertStatus(409)
        ->assertJson(['error' => 'This exam session has already been completed.']);

    expect(ExamAnswer::where('exam_attempt_id', $attempt->id)->count())->toBe(0);
});

test('rapid double exam submission is idempotent and safe under concurrency', function () {
    $candidate = User::factory()->create(['affiliation' => 'internal']);

    $session = TestSession::create([
        'title' => 'TOEFL Test Session',
        'is_active' => true,
    ]);

    $section = Section::create([
        'name' => 'Listening Comprehension',
        'slug' => 'listening',
        'duration_minutes' => 35,
        'order' => 1,
    ]);
    $session->sections()->attach($section->id);

    $attempt = ExamAttempt::create([
        'user_id' => $candidate->id,
        'test_session_id' => $session->id,
        'current_section_slug' => 'listening',
        'status' => 'in_progress',
        'started_at' => now(),
    ]);

    // First submit
    $firstResponse = $this->actingAs($candidate)->get(route('exam.submit', $attempt));
    $firstResponse->assertRedirect(route('exam.result', $attempt));

    $attempt->refresh();
    expect($attempt->status)->toBe('completed');

    // Second rapid submit (simulating double click or duplicate network packet)
    $secondResponse = $this->actingAs($candidate)->get(route('exam.submit', $attempt));
    $secondResponse->assertRedirect(route('exam.result', $attempt));

    // Status remains completed cleanly
    expect($attempt->fresh()->status)->toBe('completed');
});

test('pessimistic lock on test session prevents race conditions on capacity', function () {
    $session = TestSession::create([
        'title' => 'Capped Session',
        'is_active' => true,
        'capacity' => 1,
    ]);

    $candidateA = User::factory()->create();
    $candidateB = User::factory()->create();

    // Candidate A grabs the last seat
    $this->actingAs($candidateA)->post(route('exam.start', $session));

    expect(ExamAttempt::where('test_session_id', $session->id)->count())->toBe(1);

    // Candidate B tries simultaneously
    $responseB = $this->actingAs($candidateB)->post(route('exam.start', $session));

    $responseB->assertRedirect(route('student.dashboard'));
    $responseB->assertSessionHas('error');

    // Capacity was not exceeded
    expect(ExamAttempt::where('test_session_id', $session->id)->count())->toBe(1);
});

test('single active session terminates prior session on concurrent device login', function () {
    $student = User::create([
        'name' => 'Device Test Student',
        'email' => 'device@university.edu',
        'password' => 'secret1234',
        'role' => 'student',
        'email_verified_at' => now(),
    ]);

    // Simulate active session on Laptop A
    DB::table('sessions')->insert([
        'id' => 'laptop-a-session-id',
        'user_id' => $student->id,
        'ip_address' => '192.168.1.10',
        'user_agent' => 'Mozilla/5.0 Laptop A',
        'payload' => 'payload-data',
        'last_activity' => time(),
    ]);

    expect(DB::table('sessions')->where('user_id', $student->id)->count())->toBe(1);

    // Login from Laptop B
    $this->post(route('student.login.submit'), [
        'email' => 'device@university.edu',
        'password' => 'secret1234',
    ]);

    // Older session on Laptop A is purged
    expect(DB::table('sessions')->where('id', 'laptop-a-session-id')->exists())->toBeFalse();
});
