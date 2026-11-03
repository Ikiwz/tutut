<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirect(): RedirectResponse
    {
        if (auth()->check()) {
            return auth()->user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('student.dashboard');
        }

        if (empty(config('services.google.client_id')) || empty(config('services.google.client_secret'))) {
            return redirect()->route('student.login')->with('error', 'Google authentication is currently unavailable.');
        }

        try {
            return Socialite::driver('google')->redirect();
        } catch (Exception $e) {
            return redirect()->route('student.login')->with('error', 'Unable to connect to Google authentication.');
        }
    }

    /**
     * Obtain the user information from Google and authenticate/register the user.
     */
    public function callback(Request $request): RedirectResponse
    {
        if (auth()->check()) {
            return auth()->user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('student.dashboard');
        }

        try {
            /** @var \Laravel\Socialite\Two\User $googleUser */
            $googleUser = Socialite::driver('google')->user();
        } catch (Exception $e) {
            return redirect()->route('student.login')->with('error', 'Google authentication failed or was cancelled.');
        }

        $email = $googleUser->getEmail();
        if (empty($email)) {
            return redirect()->route('student.login')->with('error', 'No email address was provided by Google.');
        }

        // Locate existing candidate by google_id or email
        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $email)
            ->first();

        if ($user) {
            // Update Google ID and Avatar if not set
            $updated = false;
            if (empty($user->google_id)) {
                $user->google_id = $googleUser->getId();
                $updated = true;
            }
            if (empty($user->avatar) && ! empty($googleUser->getAvatar())) {
                $user->avatar = $googleUser->getAvatar();
                $updated = true;
            }
            if ($updated) {
                $user->save();
            }
        } else {
            // Register new candidate via Google OAuth
            $user = User::create([
                'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'Candidate',
                'email' => $email,
                'password' => Hash::make(Str::random(32)),
                'role' => 'student',
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'email_verified_at' => now(),
            ]);
        }

        // Authenticate user session
        Auth::login($user, remember: true);
        $request->session()->regenerate();
        $request->session()->flash('login_success', true);

        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('student.dashboard'));
    }
}
