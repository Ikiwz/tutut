<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::where('role', 'student')->withCount('examAttempts');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('student_id', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->input('status') === 'verified') {
            $query->whereNotNull('verified_at');
        } elseif ($request->input('status') === 'unverified') {
            $query->whereNull('verified_at');
        }

        $students = $query->latest()->paginate(15)->withQueryString();

        return view('admin.students.index', compact('students'));
    }

    public function verify(User $user): RedirectResponse
    {
        if ($user->role !== 'student') {
            return back()->with('error', 'Only students can be verified.');
        }

        if ($user->verified_at) {
            $user->update(['verified_at' => null]);
            $message = "Verification revoked for {$user->name}.";
        } else {
            $user->update(['verified_at' => now()]);
            $message = "Student {$user->name} has been verified successfully.";
        }

        return back()->with('success', $message);
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->role !== 'student') {
            return back()->with('error', 'Administrator accounts cannot be deleted here.');
        }

        $user->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Student record deleted successfully.');
    }
}
