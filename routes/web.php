<?php

use App\Http\Controllers\Admin\DirectionController;
use App\Http\Controllers\Admin\ExamAttemptController;
use App\Http\Controllers\Admin\PassageController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TestSessionController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\StudentAuthController;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnsureUserIsStudent;
use Illuminate\Support\Facades\Route;

// Redirect root to student login
Route::get('/', function () {
    return redirect()->route('student.login');
});

// Student Auth Routes
Route::get('/login', [StudentAuthController::class, 'showLogin'])->name('student.login');
Route::post('/login', [StudentAuthController::class, 'login'])->middleware('throttle:6,1')->name('student.login.submit');
Route::get('/register', [StudentAuthController::class, 'showRegister'])->name('student.register');
Route::post('/register', [StudentAuthController::class, 'register'])->middleware('throttle:5,1')->name('student.register.submit');
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

// Admin Auth Routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->middleware('throttle:6,1')->name('admin.login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    // Admin Protected Routes (Flux UI Dashboard)
    Route::middleware(['auth', EnsureUserIsAdmin::class])->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        // Test Management
        Route::resource('test-sessions', TestSessionController::class, ['as' => 'admin']);
        Route::resource('sections', SectionController::class, ['as' => 'admin']);
        Route::resource('questions', QuestionController::class, ['as' => 'admin']);
        Route::resource('passages', PassageController::class, ['as' => 'admin']);
        Route::resource('directions', DirectionController::class, ['as' => 'admin']);

        // Exam Attempts & Reports
        Route::get('/attempts', [ExamAttemptController::class, 'index'])->name('admin.attempts.index');
        Route::get('/attempts/{examAttempt}', [ExamAttemptController::class, 'show'])->name('admin.attempts.show');

        // Students Verification Management
        Route::get('/students', [StudentController::class, 'index'])->name('admin.students.index');
        Route::post('/students/{user}/verify', [StudentController::class, 'verify'])->name('admin.students.verify');
        Route::delete('/students/{user}', [StudentController::class, 'destroy'])->name('admin.students.destroy');
    });
});
