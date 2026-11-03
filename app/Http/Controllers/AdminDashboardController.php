<?php

namespace App\Http\Controllers;

use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\TestSession;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_students' => User::where('role', 'student')->count(),
            'total_questions' => Question::count(),
            'active_tests' => TestSession::where('is_active', true)->count(),
            'avg_score' => (int) round(ExamAttempt::where('status', 'completed')->avg('total_score') ?? 0),
            'total_attempts' => ExamAttempt::count(),
            'completed_attempts' => ExamAttempt::where('status', 'completed')->count(),
        ];

        $recentAttempts = ExamAttempt::with(['user', 'testSession'])
            ->latest()
            ->take(8)
            ->get();

        $activeTestSessions = TestSession::with('sections')
            ->withCount(['examAttempts'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentAttempts', 'activeTestSessions'));
    }
}
