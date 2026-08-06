<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::first();
$session = \App\Models\TestSession::first();

if (!$user || !$session) {
    echo "Need user and session.\n";
    exit(1);
}

$exam = \App\Models\ExamAttempt::firstOrCreate(
    ['user_id' => $user->id, 'test_session_id' => $session->id],
    ['status' => 'in_progress', 'current_section_slug' => 'listening', 'started_at' => now()]
);

echo "Exam URL: /student/exam/" . $exam->id . "\n";
