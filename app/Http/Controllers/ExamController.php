<?php

namespace App\Http\Controllers;

use App\Models\ExamAnswer;
use App\Models\ExamAttempt;
use App\Models\TestSession;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $activeTests = TestSession::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->with('sections')
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
        $user = auth()->user();

        // Check if already in progress
        $existing = ExamAttempt::where('user_id', $user->id)
            ->where('test_session_id', $testSession->id)
            ->where('status', 'in_progress')
            ->first();

        if ($existing) {
            return redirect()->route('exam.take', $existing);
        }

        $firstSection = $testSession->sections()->orderBy('sections.order')->first();

        $attempt = ExamAttempt::create([
            'user_id' => $user->id,
            'test_session_id' => $testSession->id,
            'current_section_slug' => $firstSection?->slug,
            'current_question_index' => 0,
            'started_at' => now(),
            'status' => 'in_progress',
        ]);

        return redirect()->route('exam.take', $attempt);
    }

    public function takeExam(ExamAttempt $examAttempt)
    {
        if ($examAttempt->user_id !== auth()->id()) {
            abort(403);
        }

        if ($examAttempt->status === 'completed') {
            return redirect()->route('exam.result', $examAttempt);
        }

        $testSession = $examAttempt->testSession->load('sections.directions.questions', 'sections.questions');
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
            $passages = $currentSection->passages()->orderBy('order')->get();
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

        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'selected_answer' => 'required|in:A,B,C,D',
        ]);

        $question = \App\Models\Question::findOrFail($validated['question_id']);

        ExamAnswer::updateOrCreate(
            [
                'exam_attempt_id' => $examAttempt->id,
                'question_id' => $validated['question_id'],
            ],
            [
                'selected_answer' => $validated['selected_answer'],
                'is_correct' => $validated['selected_answer'] === $question->correct_answer,
            ]
        );

        return response()->json(['success' => true]);
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

        if ($examAttempt->status !== 'completed') {
            $examAttempt->calculateScores();
        }

        return redirect()->route('exam.result', $examAttempt);
    }

    public function result(ExamAttempt $examAttempt)
    {
        if ($examAttempt->user_id !== auth()->id()) {
            abort(403);
        }

        $examAttempt->load('testSession', 'answers.question');

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
