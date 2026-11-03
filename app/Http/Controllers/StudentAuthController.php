<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class StudentAuthController extends Controller
{
    public function showLogin()
    {
        if (auth()->check()) {
            return auth()->user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('student.dashboard');
        }

        return view('student.login');
    }

    public function login(Request $request)
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
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            if ($user->isStudent()) {
                RateLimiter::clear($throttleKey);
                $request->session()->regenerate();
                $request->session()->flash('login_success', true);

                // Single active session: clean up other sessions for this student
                try {
                    DB::table('sessions')
                        ->where('user_id', $user->id)
                        ->where('id', '!=', $request->session()->getId())
                        ->delete();
                } catch (\Throwable $e) {
                    // Gracefully continue if session table is not accessible
                }

                return redirect()->intended(route('student.dashboard'));
            }

            Auth::logout();
            RateLimiter::hit($throttleKey, 60);

            return back()->with('error', 'Incorrect email or password. Please check your details and try again.')->onlyInput('email');
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->with('error', 'Incorrect email or password. Please check your details and try again.')->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('student.login')->with('success', 'You have been signed out safely.');
    }

    public function showRegister()
    {
        if (auth()->check()) {
            return auth()->user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('student.dashboard');
        }

        return view('student.register');
    }

    public function register(Request $request)
    {
        // 1. Honeypot check against automated spam bots
        if ($request->filled('portal_hp')) {
            return back()->with('error', 'Invalid request. Please try again.');
        }

        // 2. Registration rate limiter by IP (max 5 per 15 minutes)
        $registerThrottleKey = 'register|'.$request->ip();
        if (RateLimiter::tooManyAttempts($registerThrottleKey, 5)) {
            $seconds = RateLimiter::availableIn($registerThrottleKey);

            return back()
                ->with('error', "Too many registration attempts from this network. Please wait {$seconds} seconds before trying again.")
                ->onlyInput('name', 'email');
        }

        // 3. Normalize input values
        $request->merge([
            'name' => trim((string) $request->input('name', '')),
            'email' => strtolower(trim((string) $request->input('email', ''))),
        ]);

        $validated = $request->validate([
            'name' => 'required|string|min:2|max:100',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => ['required', 'string', 'min:8', 'max:64', 'confirmed'],
        ], [
            'name.required' => 'Please enter your full name.',
            'name.min' => 'Your name must be at least 2 characters.',
            'name.max' => 'Your name cannot be longer than 100 characters.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered. Please log in instead.',
            'password.required' => 'Please create a password.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.max' => 'Password cannot exceed 64 characters.',
            'password.confirmed' => 'Passwords do not match. Please try again.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'student',
        ]);

        RateLimiter::hit($registerThrottleKey, 900);

        Auth::login($user);
        $request->session()->flash('login_success', true);

        return redirect()->route('student.dashboard')->with('success', 'Welcome to TUTUT! Your account has been created.');
    }
}
