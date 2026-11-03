<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

        if ($affiliation = $request->input('affiliation')) {
            if (in_array($affiliation, ['internal', 'external'])) {
                $query->where('affiliation', $affiliation);
            }
        }

        if ($claimStatus = $request->input('claim_status')) {
            if (in_array($claimStatus, ['pending', 'approved', 'rejected'])) {
                $query->where('campus_claim_status', $claimStatus);
            }
        }

        $students = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total' => User::where('role', 'student')->count(),
            'internal' => User::where('role', 'student')->where('affiliation', 'internal')->count(),
            'external' => User::where('role', 'student')->where('affiliation', 'external')->count(),
            'pending_claims' => User::where('role', 'student')->where('campus_claim_status', 'pending')->count(),
        ];

        return view('admin.students.index', compact('students', 'stats'));
    }

    public function verify(User $user): RedirectResponse
    {
        if ($user->role !== 'student') {
            return back()->with('error', 'Only candidates can be verified.');
        }

        if ($user->verified_at) {
            $user->update(['verified_at' => null]);
            $message = "Verification revoked for {$user->name}.";
        } else {
            $user->update([
                'verified_at' => now(),
                'affiliation' => 'internal',
                'campus_claim_status' => 'approved',
            ]);
            $message = "Candidate {$user->name} has been verified as internal university student.";
        }

        return back()->with('success', $message);
    }

    public function approveClaim(User $user): RedirectResponse
    {
        if ($user->role !== 'student') {
            return back()->with('error', 'Only candidates can be verified.');
        }

        $user->update([
            'affiliation' => 'internal',
            'verified_at' => now(),
            'campus_claim_status' => 'approved',
            'campus_claim_notes' => null,
        ]);

        return back()->with('success', "Campus status claim approved for {$user->name}. Candidate now has 1 free attempt.");
    }

    public function rejectClaim(Request $request, User $user): RedirectResponse
    {
        if ($user->role !== 'student') {
            return back()->with('error', 'Only candidates can be verified.');
        }

        $reason = $request->input('reason', 'Provided credentials could not be verified with university records.');

        $user->update([
            'campus_claim_status' => 'rejected',
            'campus_claim_notes' => $reason,
        ]);

        return back()->with('success', "Campus claim for {$user->name} was rejected.");
    }

    public function downloadDocument(User $user): StreamedResponse
    {
        if (empty($user->student_card) || ! Storage::disk('local')->exists($user->student_card)) {
            abort(404, 'Verification document not found.');
        }

        return Storage::disk('local')->response($user->student_card);
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->role !== 'student') {
            return back()->with('error', 'Administrator accounts cannot be deleted here.');
        }

        $user->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Candidate record deleted successfully.');
    }
}
