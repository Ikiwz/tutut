<?php

use App\Http\Controllers\ExamController;
use App\Http\Controllers\StudentAuthController;
use App\Http\Middleware\EnsureUserIsStudent;
use Illuminate\Support\Facades\Route;

// Redirect root to student login
Route::get('/', function () {
    return redirect()->route('student.login');
});

// Student Auth Routes
Route::get('/login', [StudentAuthController::class, 'showLogin'])->name('student.login');
Route::post('/login', [StudentAuthController::class, 'login'])->name('student.login.submit');
Route::get('/register', [StudentAuthController::class, 'showRegister'])->name('student.register');
Route::post('/register', [StudentAuthController::class, 'register'])->name('student.register.submit');
Route::post('/logout', [StudentAuthController::class, 'logout'])->name('student.logout');

// Student Protected Routes
Route::middleware(['auth', EnsureUserIsStudent::class])->group(function () {
    Route::get('/dashboard', [ExamController::class, 'dashboard'])->name('student.dashboard');
    Route::post('/exam/start/{testSession}', [ExamController::class, 'startExam'])->name('exam.start');
    Route::get('/exam/{examAttempt}', [ExamController::class, 'takeExam'])->name('exam.take');
    Route::post('/exam/{examAttempt}/answer', [ExamController::class, 'saveAnswer'])->name('exam.answer');
    Route::post('/exam/{examAttempt}/next-section', [ExamController::class, 'nextSection'])->name('exam.nextSection');
    Route::get('/exam/{examAttempt}/submit', [ExamController::class, 'submitExam'])->name('exam.submit');
    Route::get('/exam/{examAttempt}/result', [ExamController::class, 'result'])->name('exam.result');
});
