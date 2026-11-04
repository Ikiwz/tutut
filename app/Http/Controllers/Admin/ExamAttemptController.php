<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExamAttemptController extends Controller
{
    public function index(Request $request): View
    {
        $query = ExamAttempt::with(['user', 'testSession']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('serial_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('student_id', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $attempts = $query->latest()->paginate(15)->withQueryString();

        return view('admin.attempts.index', compact('attempts'));
    }

    public function show(ExamAttempt $examAttempt): View
    {
        $examAttempt->load(['user', 'testSession.sections.questions', 'answers.question', 'integrityLogs']);

        return view('admin.attempts.show', compact('examAttempt'));
    }
}
