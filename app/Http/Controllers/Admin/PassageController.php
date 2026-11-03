<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Passage;
use App\Models\Section;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PassageController extends Controller
{
    public function index(Request $request): View
    {
        $query = Passage::with('section')->withCount('questions');

        if ($sectionId = $request->input('section_id')) {
            $query->where('section_id', $sectionId);
        }

        if ($search = $request->input('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        $passages = $query->orderBy('order')->paginate(10)->withQueryString();
        $sections = Section::orderBy('order')->get();

        return view('admin.passages.index', compact('passages', 'sections'));
    }

    public function create(): View
    {
        $sections = Section::orderBy('order')->get();

        return view('admin.passages.create', compact('sections'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'section_id' => 'required|exists:sections,id',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'audio_path' => 'nullable|file|mimes:mp3,wav,ogg,m4a,aac,webm|max:512000',
            'order' => 'required|integer|min:0',
        ]);

        $audioPath = $request->hasFile('audio_path')
            ? $request->file('audio_path')->store('passage-audio', 'public')
            : null;

        Passage::create([
            'section_id' => $validated['section_id'],
            'title' => $validated['title'],
            'content' => $validated['content'] ?? null,
            'audio_path' => $audioPath,
            'order' => $validated['order'],
        ]);

        return redirect()->route('admin.passages.index')
            ->with('success', 'Passage created successfully.');
    }

    public function edit(Passage $passage): View
    {
        $sections = Section::orderBy('order')->get();

        return view('admin.passages.edit', compact('passage', 'sections'));
    }

    public function update(Request $request, Passage $passage): RedirectResponse
    {
        $validated = $request->validate([
            'section_id' => 'required|exists:sections,id',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'audio_path' => 'nullable|file|mimes:mp3,wav,ogg,m4a,aac,webm|max:512000',
            'order' => 'required|integer|min:0',
        ]);

        $data = [
            'section_id' => $validated['section_id'],
            'title' => $validated['title'],
            'content' => $validated['content'] ?? null,
            'order' => $validated['order'],
        ];

        if ($request->hasFile('audio_path')) {
            $data['audio_path'] = $request->file('audio_path')->store('passage-audio', 'public');
        }

        $passage->update($data);

        return redirect()->route('admin.passages.index')
            ->with('success', 'Passage updated successfully.');
    }

    public function destroy(Passage $passage): RedirectResponse
    {
        $passage->delete();

        return redirect()->route('admin.passages.index')
            ->with('success', 'Passage deleted successfully.');
    }
}
