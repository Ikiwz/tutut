<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SectionController extends Controller
{
    public function index(): View
    {
        $sections = Section::withCount(['directions', 'passages', 'questions'])
            ->orderBy('order')
            ->get();

        return view('admin.sections.index', compact('sections'));
    }

    public function create(): View
    {
        return view('admin.sections.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:sections,slug',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'order' => 'required|integer|min:0',
        ]);

        Section::create($validated);

        return redirect()->route('admin.sections.index')
            ->with('success', 'Section created successfully.');
    }

    public function edit(Section $section): View
    {
        return view('admin.sections.edit', compact('section'));
    }

    public function update(Request $request, Section $section): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => ['required', 'string', 'max:255', Rule::unique('sections', 'slug')->ignore($section->id)],
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'order' => 'required|integer|min:0',
        ]);

        $section->update($validated);

        return redirect()->route('admin.sections.index')
            ->with('success', 'Section updated successfully.');
    }

    public function destroy(Section $section): RedirectResponse
    {
        $section->delete();

        return redirect()->route('admin.sections.index')
            ->with('success', 'Section deleted successfully.');
    }
}
