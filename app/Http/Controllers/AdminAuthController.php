<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('student.dashboard');
        }

        return view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        // 1. Normalize input
        $request->merge([
            'email' => strtolower(trim((string) $request->input('email', ''))),
        ]);

        $throttleKey = Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());

        // 2. Check Rate Limiter (Max 5 attempts per minute)
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()
                ->with('error', "Too many unsuccessful login attempts. Please wait {$seconds} seconds before trying again.")
                ->onlyInput('email');
        }

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if ($user->isAdmin()) {
                RateLimiter::clear($throttleKey);
                $request->session()->regenerate();

                // Single active session: clean up other sessions for this admin
                try {
                    DB::table('sessions')
                        ->where('user_id', $user->id)
                        ->where('id', '!=', $request->session()->getId())
                        ->delete();
                } catch (\Throwable $e) {
                    // Gracefully continue if session table is not accessible
                }

                return redirect()->intended(route('admin.dashboard'));
            }

            Auth::logout();
            RateLimiter::hit($throttleKey, 60);

            return back()->with('error', 'Incorrect email or password. Please check your details and try again.')->onlyInput('email');
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->with('error', 'Incorrect email or password. Please check your details and try again.')->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'You have been signed out safely.');
    }
}
