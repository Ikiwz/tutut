<?php

use App\Http\Controllers\Admin\DirectionController;
use App\Http\Controllers\Admin\ExamAttemptController;
use App\Http\Controllers\Admin\PassageController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TestSessionController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\CandidateAffiliationAndPaymentController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\StudentAuthController;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnsureUserIsStudent;
use App\Http\Middleware\PreventBackForwardCache;
use Illuminate\Support\Facades\Route;

// Redirect root to role-based dashboard or student login
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('student.dashboard');
    }

    return redirect()->route('student.login');
});

// Public Guidelines & Policy Routes
Route::get('/terms', [LegalController::class, 'terms'])->name('terms');
Route::get('/privacy', [LegalController::class, 'privacy'])->name('privacy');
Route::get('/exam-rules', [LegalController::class, 'rules'])->name('rules');
Route::get('/faq', [LegalController::class, 'faq'])->name('faq');

// Student Guest Auth Routes
Route::middleware(['guest', PreventBackForwardCache::class])->group(function () {
    Route::get('/login', [StudentAuthController::class, 'showLogin'])->name('student.login');
    Route::post('/login', [StudentAuthController::class, 'login'])->middleware('throttle:6,1')->name('student.login.submit');
    Route::get('/register', [StudentAuthController::class, 'showRegister'])->name('student.register');
    Route::post('/register', [StudentAuthController::class, 'register'])->middleware('throttle:5,1')->name('student.register.submit');

    // Google OAuth Routes
    Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');

    // Password Reset Routes
    Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->middleware('throttle:5,1')->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

// Student Logout
Route::post('/logout', [StudentAuthController::class, 'logout'])->middleware('auth')->name('student.logout');

// Email Verification Routes
Route::middleware(['auth', PreventBackForwardCache::class])->group(function () {
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

// Student Protected Routes
Route::middleware(['auth', EnsureUserIsStudent::class, 'verified', PreventBackForwardCache::class])->group(function () {
    Route::get('/dashboard', [ExamController::class, 'dashboard'])->name('student.dashboard');
    Route::post('/claim-campus', [CandidateAffiliationAndPaymentController::class, 'claimCampus'])->middleware('throttle:5,1')->name('student.claim-campus');
    Route::post('/payment/submit', [CandidateAffiliationAndPaymentController::class, 'submitPayment'])->middleware('throttle:5,1')->name('student.payment.submit');
    Route::get('/my-document', [CandidateAffiliationAndPaymentController::class, 'viewMyStudentCard'])->name('student.document');
    Route::post('/exam/start/{testSession}', [ExamController::class, 'startExam'])->middleware('throttle:30,1')->name('exam.start');
    Route::get('/exam/{examAttempt}', [ExamController::class, 'takeExam'])->name('exam.take');
    Route::post('/exam/{examAttempt}/answer', [ExamController::class, 'saveAnswer'])->middleware('throttle:120,1')->name('exam.answer');
    Route::post('/exam/{examAttempt}/integrity-log', [ExamController::class, 'recordIntegrityViolation'])->middleware('throttle:120,1')->name('exam.integrity.log');
    Route::post('/exam/{examAttempt}/next-section', [ExamController::class, 'nextSection'])->middleware('throttle:30,1')->name('exam.nextSection');
    Route::get('/exam/{examAttempt}/submit', [ExamController::class, 'submitExam'])->middleware('throttle:10,1')->name('exam.submit');
    Route::get('/exam/{examAttempt}/result', [ExamController::class, 'result'])->name('exam.result');
});

// Admin Routes
Route::prefix('admin')->group(function () {
    // Admin Guest Auth Routes
    Route::middleware(['guest', PreventBackForwardCache::class])->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
        Route::post('/login', [AdminAuthController::class, 'login'])->middleware('throttle:5,15')->name('admin.login.submit');
    });

    // Admin Logout
    Route::post('/logout', [AdminAuthController::class, 'logout'])->middleware('auth')->name('admin.logout');

    // Admin Protected Routes (Flux UI Dashboard)
    Route::middleware(['auth', EnsureUserIsAdmin::class, PreventBackForwardCache::class])->group(function () {
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
        Route::post('/students/{user}/approve-claim', [StudentController::class, 'approveClaim'])->name('admin.students.approve-claim');
        Route::post('/students/{user}/reject-claim', [StudentController::class, 'rejectClaim'])->name('admin.students.reject-claim');
        Route::get('/students/{user}/document', [StudentController::class, 'downloadDocument'])->name('admin.students.document');
        Route::delete('/students/{user}', [StudentController::class, 'destroy'])->name('admin.students.destroy');

        // Payment Verification Management
        Route::get('/payments', [PaymentController::class, 'index'])->name('admin.payments.index');
        Route::post('/payments/{payment}/approve', [PaymentController::class, 'approve'])->name('admin.payments.approve');
        Route::post('/payments/{payment}/reject', [PaymentController::class, 'reject'])->name('admin.payments.reject');
        Route::get('/payments/{payment}/proof', [PaymentController::class, 'downloadProof'])->name('admin.payments.proof');
    });
});
