<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Direction;
use App\Models\Section;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DirectionController extends Controller
{
    public function index(Request $request): View
    {
        $query = Direction::with('section')->withCount('questions');

        if ($sectionId = $request->input('section_id')) {
            $query->where('section_id', $sectionId);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('label', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%");
            });
        }

        $directions = $query->orderBy('order')->paginate(10)->withQueryString();
        $sections = Section::orderBy('order')->get();

        return view('admin.directions.index', compact('directions', 'sections'));
    }

    public function create(): View
    {
        $sections = Section::orderBy('order')->get();

        return view('admin.directions.create', compact('sections'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'section_id' => 'required|exists:sections,id',
            'label' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'audio_path' => 'nullable|file|mimes:mp3,wav,ogg,m4a,aac,webm|max:512000',
            'order' => 'required|integer|min:0',
        ]);

        $audioPath = $request->hasFile('audio_path')
            ? $request->file('audio_path')->store('directions-audio', 'public')
            : null;

        Direction::create([
            'section_id' => $validated['section_id'],
            'label' => $validated['label'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'audio_path' => $audioPath,
            'order' => $validated['order'],
        ]);

        return redirect()->route('admin.directions.index')
            ->with('success', 'Direction created successfully.');
    }

    public function edit(Direction $direction): View
    {
        $sections = Section::orderBy('order')->get();

        return view('admin.directions.edit', compact('direction', 'sections'));
    }

    public function update(Request $request, Direction $direction): RedirectResponse
    {
        $validated = $request->validate([
            'section_id' => 'required|exists:sections,id',
            'label' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'audio_path' => 'nullable|file|mimes:mp3,wav,ogg,m4a,aac,webm|max:512000',
            'order' => 'required|integer|min:0',
        ]);

        $data = [
            'section_id' => $validated['section_id'],
            'label' => $validated['label'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'order' => $validated['order'],
        ];

        if ($request->hasFile('audio_path')) {
            $data['audio_path'] = $request->file('audio_path')->store('directions-audio', 'public');
        }

        $direction->update($data);

        return redirect()->route('admin.directions.index')
            ->with('success', 'Direction updated successfully.');
    }

    public function destroy(Direction $direction): RedirectResponse
    {
        $direction->delete();

        return redirect()->route('admin.directions.index')
            ->with('success', 'Direction deleted successfully.');
    }
}
