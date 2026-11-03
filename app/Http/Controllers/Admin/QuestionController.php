<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Direction;
use App\Models\ExamKey;
use App\Models\Passage;
use App\Models\Question;
use App\Models\Section;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuestionController extends Controller
{
    public function index(Request $request): View
    {
        $query = Question::with(['section', 'direction', 'examKey']);

        if ($sectionId = $request->input('section_id')) {
            $query->where('section_id', $sectionId);
        }

        if ($search = $request->input('search')) {
            $query->where('question_text', 'like', "%{$search}%");
        }

        $questions = $query->orderBy('section_id')->orderBy('order')->paginate(15)->withQueryString();
        $sections = Section::orderBy('order')->get();

        return view('admin.questions.index', compact('questions', 'sections'));
    }

    public function create(): View
    {
        $sections = Section::orderBy('order')->get();
        $directions = Direction::orderBy('order')->get();
        $passages = Passage::orderBy('order')->get();

        return view('admin.questions.create', compact('sections', 'directions', 'passages'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'section_id' => 'required|exists:sections,id',
            'direction_id' => 'nullable|exists:directions,id',
            'passage_id' => 'nullable|exists:passages,id',
            'question_text' => 'nullable|string',
            'audio_path' => 'nullable|file|mimes:mp3,wav,ogg,m4a,aac,webm|max:512000',
            'option_a' => 'required|string',
            'option_a_audio' => 'nullable|file|mimes:mp3,wav,ogg,m4a,aac,webm|max:512000',
            'option_b' => 'required|string',
            'option_b_audio' => 'nullable|file|mimes:mp3,wav,ogg,m4a,aac,webm|max:512000',
            'option_c' => 'required|string',
            'option_c_audio' => 'nullable|file|mimes:mp3,wav,ogg,m4a,aac,webm|max:512000',
            'option_d' => 'required|string',
            'option_d_audio' => 'nullable|file|mimes:mp3,wav,ogg,m4a,aac,webm|max:512000',
            'correct_answer' => 'required|in:A,B,C,D',
            'order' => 'required|integer|min:0',
        ]);

        $audioPath = $request->hasFile('audio_path')
            ? $request->file('audio_path')->store('question-audio', 'public')
            : null;

        $optionAAudio = $request->hasFile('option_a_audio')
            ? $request->file('option_a_audio')->store('question-audio', 'public')
            : null;
        $optionBAudio = $request->hasFile('option_b_audio')
            ? $request->file('option_b_audio')->store('question-audio', 'public')
            : null;
        $optionCAudio = $request->hasFile('option_c_audio')
            ? $request->file('option_c_audio')->store('question-audio', 'public')
            : null;
        $optionDAudio = $request->hasFile('option_d_audio')
            ? $request->file('option_d_audio')->store('question-audio', 'public')
            : null;

        $question = Question::create([
            'section_id' => $validated['section_id'],
            'direction_id' => $validated['direction_id'] ?? null,
            'passage_id' => $validated['passage_id'] ?? null,
            'question_text' => $validated['question_text'] ?? null,
            'audio_path' => $audioPath,
            'option_a' => $validated['option_a'],
            'option_a_audio' => $optionAAudio,
            'option_b' => $validated['option_b'],
            'option_b_audio' => $optionBAudio,
            'option_c' => $validated['option_c'],
            'option_c_audio' => $optionCAudio,
            'option_d' => $validated['option_d'],
            'option_d_audio' => $optionDAudio,
            'correct_answer' => $validated['correct_answer'],
            'order' => $validated['order'],
        ]);

        // Secure Key Vault
        ExamKey::updateOrCreate(
            ['question_id' => $question->id],
            ['correct_key' => $validated['correct_answer']]
        );

        return redirect()->route('admin.questions.index')
            ->with('success', 'Question created successfully and securely vaulted.');
    }

    public function edit(Question $question): View
    {
        $question->load(['section', 'direction', 'passage', 'examKey']);
        $sections = Section::orderBy('order')->get();
        $directions = Direction::orderBy('order')->get();
        $passages = Passage::orderBy('order')->get();

        return view('admin.questions.edit', compact('question', 'sections', 'directions', 'passages'));
    }

    public function update(Request $request, Question $question): RedirectResponse
    {
        $validated = $request->validate([
            'section_id' => 'required|exists:sections,id',
            'direction_id' => 'nullable|exists:directions,id',
            'passage_id' => 'nullable|exists:passages,id',
            'question_text' => 'nullable|string',
            'audio_path' => 'nullable|file|mimes:mp3,wav,ogg,m4a,aac,webm|max:512000',
            'option_a' => 'required|string',
            'option_a_audio' => 'nullable|file|mimes:mp3,wav,ogg,m4a,aac,webm|max:512000',
            'option_b' => 'required|string',
            'option_b_audio' => 'nullable|file|mimes:mp3,wav,ogg,m4a,aac,webm|max:512000',
            'option_c' => 'required|string',
            'option_c_audio' => 'nullable|file|mimes:mp3,wav,ogg,m4a,aac,webm|max:512000',
            'option_d' => 'required|string',
            'option_d_audio' => 'nullable|file|mimes:mp3,wav,ogg,m4a,aac,webm|max:512000',
            'correct_answer' => 'required|in:A,B,C,D',
            'order' => 'required|integer|min:0',
        ]);

        $data = [
            'section_id' => $validated['section_id'],
            'direction_id' => $validated['direction_id'] ?? null,
            'passage_id' => $validated['passage_id'] ?? null,
            'question_text' => $validated['question_text'] ?? null,
            'option_a' => $validated['option_a'],
            'option_b' => $validated['option_b'],
            'option_c' => $validated['option_c'],
            'option_d' => $validated['option_d'],
            'correct_answer' => $validated['correct_answer'],
            'order' => $validated['order'],
        ];

        if ($request->hasFile('audio_path')) {
            $data['audio_path'] = $request->file('audio_path')->store('question-audio', 'public');
        }

        if ($request->hasFile('option_a_audio')) {
            $data['option_a_audio'] = $request->file('option_a_audio')->store('question-audio', 'public');
        }
        if ($request->hasFile('option_b_audio')) {
            $data['option_b_audio'] = $request->file('option_b_audio')->store('question-audio', 'public');
        }
        if ($request->hasFile('option_c_audio')) {
            $data['option_c_audio'] = $request->file('option_c_audio')->store('question-audio', 'public');
        }
        if ($request->hasFile('option_d_audio')) {
            $data['option_d_audio'] = $request->file('option_d_audio')->store('question-audio', 'public');
        }

        $question->update($data);

        // Update Key Vault
        ExamKey::updateOrCreate(
            ['question_id' => $question->id],
            ['correct_key' => $validated['correct_answer']]
        );

        return redirect()->route('admin.questions.index')
            ->with('success', 'Question updated successfully.');
    }

    public function destroy(Question $question): RedirectResponse
    {
        $question->delete();

        return redirect()->route('admin.questions.index')
            ->with('success', 'Question deleted successfully.');
    }
}
