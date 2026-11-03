<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    /**
     * Display the form to request a password reset link.
     */
    public function showLinkRequestForm(): View|RedirectResponse
    {
        if (auth()->check()) {
            return auth()->user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('student.dashboard');
        }

        return view('auth.forgot-password');
    }

    /**
     * Send a reset link to the given user.
     */
    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        // 1. Normalize email input
        $request->merge([
            'email' => strtolower(trim((string) $request->input('email', ''))),
        ]);

        // 2. Throttle reset link requests (max 3 per 5 minutes per IP/email)
        $throttleKey = 'forgot-password|'.Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()
                ->with('error', "Too many password reset requests. Please wait {$seconds} seconds before trying again.")
                ->onlyInput('email');
        }

        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
        ]);

        RateLimiter::hit($throttleKey, 300);

        // Send reset link using default broker (privacy-preserving: identical confirmation response)
        Password::broker()->sendResetLink(
            $request->only('email')
        );

        return back()->with('success', 'If an account exists for that email, a password reset link has been sent.');
    }

    /**
     * Display the password reset form.
     */
    public function showResetForm(Request $request, ?string $token = null): View|RedirectResponse
    {
        if (auth()->check()) {
            return auth()->user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('student.dashboard');
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => strtolower(trim((string) $request->query('email', ''))),
        ]);
    }

    /**
     * Reset the given user's password.
     */
    public function reset(Request $request): RedirectResponse
    {
        // 1. Normalize email input
        $request->merge([
            'email' => strtolower(trim((string) $request->input('email', ''))),
        ]);

        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'string', 'min:8', 'max:64', 'confirmed'],
        ], [
            'token.required' => 'Invalid or missing password reset token.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'Please enter a new password.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.max' => 'Password cannot exceed 64 characters.',
            'password.confirmed' => 'Passwords do not match. Please try again.',
        ]);

        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                // Clean up any older sessions for security across devices
                try {
                    DB::table('sessions')->where('user_id', $user->id)->delete();
                } catch (\Throwable $e) {
                    // Gracefully continue
                }
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('student.login')
                ->with('success', 'Your password has been reset successfully. Please log in with your new password.');
        }

        return back()
            ->with('error', 'This password reset link is invalid or has expired. Please request a new one.')
            ->onlyInput('email');
    }
}
