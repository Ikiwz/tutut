<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\TestSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TestSessionController extends Controller
{
    public function index(Request $request): View
    {
        $query = TestSession::with('sections')->withCount('examAttempts');

        if ($search = $request->input('search')) {
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('access_code', 'like', "%{$search}%");
        }

        $sessions = $query->latest()->paginate(10)->withQueryString();

        return view('admin.test-sessions.index', compact('sessions'));
    }

    public function create(): View
    {
        $sections = Section::orderBy('order')->get();

        return view('admin.test-sessions.create', compact('sections'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'access_code' => 'nullable|string|max:16',
            'is_active' => 'boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'sections' => 'nullable|array',
            'sections.*' => 'exists:sections,id',
        ]);

        $session = TestSession::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'access_code' => $validated['access_code'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'starts_at' => $validated['starts_at'] ?? null,
            'ends_at' => $validated['ends_at'] ?? null,
        ]);

        if (! empty($validated['sections'])) {
            $session->sections()->sync($validated['sections']);
        }

        return redirect()->route('admin.test-sessions.index')
            ->with('success', 'Test session created successfully.');
    }

    public function edit(TestSession $testSession): View
    {
        $testSession->load('sections');
        $sections = Section::orderBy('order')->get();

        return view('admin.test-sessions.edit', compact('testSession', 'sections'));
    }

    public function update(Request $request, TestSession $testSession): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'access_code' => 'nullable|string|max:16',
            'is_active' => 'boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date',
            'sections' => 'nullable|array',
            'sections.*' => 'exists:sections,id',
        ]);

        $testSession->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'access_code' => $validated['access_code'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'starts_at' => $validated['starts_at'] ?? null,
            'ends_at' => $validated['ends_at'] ?? null,
        ]);

        $testSession->sections()->sync($validated['sections'] ?? []);

        return redirect()->route('admin.test-sessions.index')
            ->with('success', 'Test session updated successfully.');
    }

    public function destroy(TestSession $testSession): RedirectResponse
    {
        $testSession->delete();

        return redirect()->route('admin.test-sessions.index')
            ->with('success', 'Test session deleted successfully.');
    }
}
