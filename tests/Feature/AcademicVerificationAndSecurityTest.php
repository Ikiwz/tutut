<?php

use App\Models\ExamAnswer;
use App\Models\ExamAttempt;
use App\Models\ExamKey;
use App\Models\Question;
use App\Models\Section;
use App\Models\TestSession;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user stores academic verification fields and enforces unique student_id', function () {
    $user1 = User::create([
        'name' => 'John Doe',
        'email' => 'john@university.edu',
        'password' => 'secret123',
        'role' => 'student',
        'student_id' => 'STU-2026-001',
        'department' => 'Computer Science',
        'student_card' => 'cards/john_card.png',
        'verified_at' => now(),
    ]);

    expect($user1->student_id)->toBe('STU-2026-001')
        ->and($user1->department)->toBe('Computer Science')
        ->and($user1->student_card)->toBe('cards/john_card.png')
        ->and($user1->verified_at)->not->toBeNull();

    // Verify soft delete works
    $user1->delete();
    expect(User::find($user1->id))->toBeNull()
        ->and(User::withTrashed()->find($user1->id))->not->toBeNull();

    // Verify duplicate student_id throws QueryException
    expect(fn () => User::create([
        'name' => 'Duplicate John',
        'email' => 'john2@university.edu',
        'password' => 'secret123',
        'role' => 'student',
        'student_id' => 'STU-2026-001',
    ]))->toThrow(QueryException::class);
});

test('exam attempt automatically generates serial_number and records proctoring telemetry', function () {
    $user = User::create([
        'name' => 'Alice Student',
        'email' => 'alice@university.edu',
        'password' => 'secret123',
        'role' => 'student',
    ]);

    $testSession = TestSession::create([
        'title' => 'Midterm TOEFL',
        'description' => 'Official Exam',
        'access_code' => 'PROCTOR-8821',
        'is_active' => true,
    ]);

    $attempt = ExamAttempt::create([
        'user_id' => $user->id,
        'test_session_id' => $testSession->id,
        'current_section_slug' => 'listening',
        'current_question_index' => 0,
        'started_at' => now(),
        'status' => 'in_progress',
        'device_token' => 'tok_abc123xyz',
        'device_fingerprint' => 'fp_998877665544',
        'ip_address' => '192.168.1.100',
        'user_agent' => 'Mozilla/5.0 Test Browser',
        'focus_violations' => 2,
        'kiosk_violations' => 1,
        'signature' => hash('sha256', 'tamper_proof_payload'),
    ]);

    expect($attempt->serial_number)->toStartWith('ATT-')
        ->and($attempt->device_token)->toBe('tok_abc123xyz')
        ->and($attempt->device_fingerprint)->toBe('fp_998877665544')
        ->and($attempt->focus_violations)->toBe(2)
        ->and($attempt->kiosk_violations)->toBe(1)
        ->and(strlen($attempt->signature))->toBe(64);
});

test('exam key vault stores uppercase choice keys and question serialization hides raw answer', function () {
    $section = Section::create([
        'name' => 'Structure & Written Expression',
        'slug' => 'structure',
        'duration_minutes' => 25,
        'order' => 2,
    ]);

    $question = Question::create([
        'section_id' => $section->id,
        'question_text' => 'The Eiffel Tower _____ in Paris.',
        'option_a' => 'is located',
        'option_b' => 'locates',
        'option_c' => 'is locating',
        'option_d' => 'located',
        'correct_answer' => 'A',
        'order' => 1,
    ]);

    $key = ExamKey::create([
        'question_id' => $question->id,
        'correct_key' => 'A',
    ]);

    expect($key->correct_key)->toBe('A')
        ->and($question->examKey->id)->toBe($key->id)
        ->and($question->toArray())->not->toHaveKey('correct_answer');
});

test('exam answer evaluation compares against exam_keys vault securely', function () {
    $user = User::create([
        'name' => 'Bob Exam',
        'email' => 'bob@university.edu',
        'password' => 'secret123',
        'role' => 'student',
    ]);

    $testSession = TestSession::create([
        'title' => 'Reading Test',
        'is_active' => true,
    ]);

    $section = Section::create([
        'name' => 'Reading Comprehension',
        'slug' => 'reading',
        'duration_minutes' => 55,
        'order' => 3,
    ]);

    $testSession->sections()->attach($section->id);

    $question = Question::create([
        'section_id' => $section->id,
        'question_text' => 'What is the main topic?',
        'option_a' => 'Biology',
        'option_b' => 'Physics',
        'option_c' => 'Astronomy',
        'option_d' => 'Chemistry',
        'correct_answer' => 'C',
        'order' => 1,
    ]);

    ExamKey::create([
        'question_id' => $question->id,
        'correct_key' => 'C',
    ]);

    $attempt = ExamAttempt::create([
        'user_id' => $user->id,
        'test_session_id' => $testSession->id,
        'current_section_slug' => 'reading',
        'current_question_index' => 0,
        'started_at' => now(),
        'status' => 'in_progress',
    ]);

    // Submit correct answer via endpoint
    $response = $this->actingAs($user)->postJson(route('exam.answer', $attempt), [
        'question_id' => $question->id,
        'selected_answer' => 'C',
    ]);

    $response->assertOk();

    $answer = ExamAnswer::where('exam_attempt_id', $attempt->id)
        ->where('question_id', $question->id)
        ->first();

    expect($answer)->not->toBeNull()
        ->and($answer->selected_answer)->toBe('C')
        ->and($answer->is_correct)->toBeTrue();

    // Submit incorrect answer
    $this->actingAs($user)->postJson(route('exam.answer', $attempt), [
        'question_id' => $question->id,
        'selected_answer' => 'A',
    ]);

    $answer->refresh();
    expect($answer->selected_answer)->toBe('A')
        ->and($answer->is_correct)->toBeFalse();
});
