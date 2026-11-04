<?php

namespace App\Http\Controllers;

use App\Models\ExamAnswer;
use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\TestSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $activeTests = TestSession::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->with('sections')
            ->orderByRaw('CASE WHEN starts_at IS NULL THEN 0 WHEN starts_at <= ? THEN 1 ELSE 2 END', [now()])
            ->orderBy('starts_at', 'asc')
            ->get();

        $completedAttempts = ExamAttempt::where('user_id', $user->id)
            ->where('status', 'completed')
            ->with('testSession')
            ->orderBy('completed_at', 'desc')
            ->get();

        $inProgressAttempt = ExamAttempt::where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->first();

        return view('student.dashboard', compact('activeTests', 'completedAttempts', 'inProgressAttempt'));
    }

    public function startExam(TestSession $testSession)
    {
        return DB::transaction(function () use ($testSession) {
            $user = auth()->user();

            $lockedSession = TestSession::where('id', $testSession->id)->lockForUpdate()->first();
            if (! $lockedSession || ! $lockedSession->is_active) {
                return redirect()->route('student.dashboard')
                    ->with('error', 'This test is not active.');
            }

            if ($lockedSession->isLocked()) {
                $formattedTime = $lockedSession->starts_at->translatedFormat('d F Y, H:i');

                return redirect()->route('student.dashboard')
                    ->with('error', "The test is locked. It will open on {$formattedTime} WIB.");
            }

            if ($lockedSession->isEnded()) {
                return redirect()->route('student.dashboard')
                    ->with('error', 'The test schedule has ended.');
            }

            // Check if already in progress
            $existing = ExamAttempt::where('user_id', $user->id)
                ->where('test_session_id', $lockedSession->id)
                ->where('status', 'in_progress')
                ->first();

            if ($existing) {
                return redirect()->route('exam.take', $existing);
            }

            if ($lockedSession->isFull()) {
                return redirect()->route('student.dashboard')
                    ->with('error', "This test session has reached full capacity ({$lockedSession->capacity} seats). Please choose another schedule.");
            }

            $eligibility = $user->isEligibleForTestSession($lockedSession);
            if (! $eligibility['eligible']) {
                return redirect()->route('student.dashboard')
                    ->with('error', $eligibility['reason']);
            }

            $firstSection = $lockedSession->sections()->orderBy('sections.order')->first();

            $attempt = ExamAttempt::create([
                'user_id' => $user->id,
                'test_session_id' => $lockedSession->id,
                'current_section_slug' => $firstSection?->slug,
                'current_question_index' => 0,
                'started_at' => now(),
                'status' => 'in_progress',
            ]);

            return redirect()->route('exam.take', $attempt);
        });
    }

    public function takeExam(ExamAttempt $examAttempt)
    {
        if ($examAttempt->user_id !== auth()->id()) {
            abort(403);
        }

        if ($examAttempt->status === 'completed') {
            return redirect()->route('exam.result', $examAttempt);
        }

        if ($examAttempt->testSession && $examAttempt->testSession->isLocked()) {
            $formattedTime = $examAttempt->testSession->starts_at->translatedFormat('d F Y, H:i');

            return redirect()->route('student.dashboard')
                ->with('error', "The test is locked. It will open on {$formattedTime} WIB.");
        }

        $cacheKey = "test_session_structure_{$examAttempt->test_session_id}";
        $testSession = Cache::remember($cacheKey, 300, function () use ($examAttempt) {
            return $examAttempt->testSession->load('sections.directions.questions', 'sections.questions', 'sections.passages');
        });
        $sections = $testSession->sections->sortBy('order')->values();

        $currentSection = $sections->firstWhere('slug', $examAttempt->current_section_slug)
            ?? $sections->first();

        $questions = $currentSection->questions->sortBy('order')->values();

        // Get directions for the current section
        $directions = $currentSection->directions->sortBy('order')->values();

        // Get existing answers
        $existingAnswers = $examAttempt->answers()
            ->whereIn('question_id', $questions->pluck('id'))
            ->pluck('selected_answer', 'question_id')
            ->toArray();

        // Get passages if reading section (multiple passages for different question groups)
        $passages = collect();
        if ($currentSection->slug === 'reading') {
            $passages = $currentSection->passages ? $currentSection->passages->sortBy('order')->values() : collect();
        }

        return view('student.exam.take', compact(
            'examAttempt', 'testSession', 'sections', 'currentSection',
            'questions', 'directions', 'existingAnswers', 'passages'
        ));
    }

    public function saveAnswer(Request $request, ExamAttempt $examAttempt)
    {
        if ($examAttempt->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($examAttempt->status === 'completed') {
            return response()->json(['error' => 'This exam session has already been completed.'], 409);
        }

        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'selected_answer' => 'required|in:A,B,C,D',
        ]);

        $question = Question::with('examKey')->findOrFail($validated['question_id']);

        $correctAnswer = $question->examKey?->correct_key ?? $question->correct_answer;

        ExamAnswer::updateOrCreate(
            [
                'exam_attempt_id' => $examAttempt->id,
                'question_id' => $validated['question_id'],
            ],
            [
                'selected_answer' => $validated['selected_answer'],
                'is_correct' => $validated['selected_answer'] === $correctAnswer,
            ]
        );

        return response()->json(['success' => true]);
    }

    public function recordIntegrityViolation(Request $request, ExamAttempt $examAttempt)
    {
        if ($examAttempt->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($examAttempt->status === 'completed') {
            return response()->json(['error' => 'Attempt already completed.'], 409);
        }

        $validated = $request->validate([
            'event_type' => 'required|string|max:50',
            'severity' => 'required|in:minor,medium,severe',
            'title' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'section_slug' => 'nullable|string|max:50',
            'question_number' => 'nullable|integer',
            'metadata' => 'nullable|array',
        ]);

        $log = $examAttempt->logViolation(
            eventType: $validated['event_type'],
            severity: $validated['severity'],
            title: $validated['title'],
            description: $validated['description'] ?? null,
            sectionSlug: $validated['section_slug'] ?? null,
            questionNumber: $validated['question_number'] ?? null,
            metadata: $validated['metadata'] ?? []
        );

        return response()->json([
            'success' => true,
            'log_id' => $log->id,
            'focus_violations' => $examAttempt->focus_violations,
            'kiosk_violations' => $examAttempt->kiosk_violations,
        ]);
    }

    public function nextSection(Request $request, ExamAttempt $examAttempt)
    {
        if ($examAttempt->user_id !== auth()->id()) {
            abort(403);
        }

        $sections = $examAttempt->testSession->sections()->orderBy('sections.order')->get();
        $currentIndex = $sections->pluck('slug')->search($examAttempt->current_section_slug);

        if ($currentIndex !== false && $currentIndex < $sections->count() - 1) {
            $nextSection = $sections[$currentIndex + 1];
            $examAttempt->update([
                'current_section_slug' => $nextSection->slug,
                'current_question_index' => 0,
            ]);

            return redirect()->route('exam.take', $examAttempt);
        }

        // Last section - complete the exam
        return redirect()->route('exam.submit', $examAttempt);
    }

    public function submitExam(ExamAttempt $examAttempt)
    {
        if ($examAttempt->user_id !== auth()->id()) {
            abort(403);
        }

        DB::transaction(function () use ($examAttempt) {
            $lockedAttempt = ExamAttempt::where('id', $examAttempt->id)->lockForUpdate()->first();
            if ($lockedAttempt && $lockedAttempt->status !== 'completed') {
                $lockedAttempt->calculateScores();
            }
        });

        return redirect()->route('exam.result', $examAttempt);
    }

    public function result(ExamAttempt $examAttempt)
    {
        if ($examAttempt->user_id !== auth()->id()) {
            abort(403);
        }

        $examAttempt->load('testSession', 'answers.question', 'integrityLogs');

        $sections = $examAttempt->testSession->sections()->orderBy('sections.order')->get();

        $sectionResults = [];
        foreach ($sections as $section) {
            $questionIds = $section->questions()->pluck('id');
            $answers = $examAttempt->answers()->whereIn('question_id', $questionIds)->get();
            $sectionResults[$section->slug] = [
                'name' => $section->name,
                'total' => $questionIds->count(),
                'answered' => $answers->count(),
                'correct' => $answers->where('is_correct', true)->count(),
            ];
        }

        return view('student.exam.result', compact('examAttempt', 'sectionResults'));
    }
}
