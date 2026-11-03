<?php

namespace App\Http\Controllers;

use App\Models\ExamPayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CandidateAffiliationAndPaymentController extends Controller
{
    /**
     * Submit an internal campus affiliation claim (NIM, department, student card / proof).
     */
    public function claimCampus(Request $request): RedirectResponse
    {
        $user = auth()->user();

        if ($user->isInternal()) {
            return redirect()->route('student.dashboard')
                ->with('info', 'Your account is already verified as an internal university candidate.');
        }

        $validated = $request->validate([
            'student_id' => 'required|string|max:30|unique:users,student_id,'.$user->id,
            'department' => 'required|string|max:100',
            'student_card' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [
            'student_id.required' => 'Please enter your Student Identification Number (NIM).',
            'student_id.unique' => 'This NIM is already registered to another candidate.',
            'department.required' => 'Please specify your department or study program.',
            'student_card.required' => 'Please upload a photo of your Student ID (KTM) or defense verification letter.',
            'student_card.mimes' => 'Document must be an image (JPG, PNG) or PDF document.',
            'student_card.max' => 'Document size cannot exceed 2 MB.',
        ]);

        $path = $request->file('student_card')->store('student_cards', 'local');

        $user->update([
            'student_id' => $validated['student_id'],
            'department' => $validated['department'],
            'student_card' => $path,
            'campus_claim_status' => 'pending',
            'campus_claim_notes' => null,
        ]);

        return redirect()->route('student.dashboard')
            ->with('success', 'Your campus affiliation claim has been submitted. Our admissions team will review your credentials.');
    }

    /**
     * Submit proof of payment for an exam attempt or specific session.
     */
    public function submitPayment(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'test_session_id' => 'nullable|exists:test_sessions,id',
            'payment_method' => 'required|string|max:50',
            'amount' => 'required|numeric|min:10000',
            'proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [
            'payment_method.required' => 'Please specify your payment method or issuing bank.',
            'amount.required' => 'Please confirm the transferred payment amount.',
            'proof.required' => 'Please upload your payment receipt or transfer proof.',
            'proof.mimes' => 'Payment receipt must be an image (JPG, PNG) or PDF.',
            'proof.max' => 'Payment receipt size cannot exceed 2 MB.',
        ]);

        $path = $request->file('proof')->store('payment_proofs', 'local');

        ExamPayment::create([
            'user_id' => $user->id,
            'test_session_id' => $validated['test_session_id'] ?? null,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'proof_path' => $path,
            'status' => 'pending',
        ]);

        return redirect()->route('student.dashboard')
            ->with('success', 'Your payment receipt has been submitted. It will be verified shortly by our finance proctors.');
    }

    /**
     * Download or view candidate's own uploaded student card.
     */
    public function viewMyStudentCard(): StreamedResponse
    {
        $user = auth()->user();

        if (empty($user->student_card) || ! Storage::disk('local')->exists($user->student_card)) {
            abort(404, 'Document not found.');
        }

        return Storage::disk('local')->response($user->student_card);
    }
}
