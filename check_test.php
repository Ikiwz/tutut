<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::first();
echo "User: " . ($user ? $user->email : 'None') . "\n";
echo "Exams: " . \App\Models\ExamAttempt::count() . "\n";
echo "Sessions: " . \App\Models\TestSession::count() . "\n";

if (\App\Models\ExamAttempt::count() > 0) {
    $exam = \App\Models\ExamAttempt::first();
    echo "First Exam URL: /student/exam/" . $exam->id . "\n";
}
