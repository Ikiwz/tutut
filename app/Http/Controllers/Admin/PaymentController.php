<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamPayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $query = ExamPayment::with(['user', 'testSession', 'verifiedBy'])->latest();

        if ($status = $request->input('status')) {
            if (in_array($status, ['pending', 'approved', 'rejected'])) {
                $query->where('status', $status);
            }
        }

        if ($search = $request->input('search')) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        $payments = $query->paginate(15)->withQueryString();

        $stats = [
            'pending' => ExamPayment::where('status', 'pending')->count(),
            'approved' => ExamPayment::where('status', 'approved')->count(),
            'rejected' => ExamPayment::where('status', 'rejected')->count(),
        ];

        return view('admin.payments.index', compact('payments', 'stats'));
    }

    public function approve(ExamPayment $payment): RedirectResponse
    {
        $payment->update([
            'status' => 'approved',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'rejection_reason' => null,
        ]);

        return back()->with('success', "Payment #{$payment->id} for {$payment->user->name} has been approved.");
    }

    public function reject(Request $request, ExamPayment $payment): RedirectResponse
    {
        $reason = $request->input('reason', 'Payment proof could not be verified.');

        $payment->update([
            'status' => 'rejected',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'rejection_reason' => $reason,
        ]);

        return back()->with('success', "Payment #{$payment->id} was rejected.");
    }

    public function downloadProof(ExamPayment $payment): StreamedResponse
    {
        if (empty($payment->proof_path) || ! Storage::disk('local')->exists($payment->proof_path)) {
            abort(404, 'Payment proof document not found.');
        }

        return Storage::disk('local')->response($payment->proof_path);
    }
}
