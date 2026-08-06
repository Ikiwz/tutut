<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class StudentAuthController extends Controller
{
    public function showLogin()
    {
        if (auth()->check() && auth()->user()->isStudent()) {
            return redirect()->route('student.dashboard');
        }
        return view('student.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->isStudent()) {
                $request->session()->regenerate();
                $request->session()->flash('login_success', true);
                return redirect()->intended(route('student.dashboard'));
            }
            Auth::logout();
            return back()->withErrors([
                'email' => 'This account is not registered as a student.',
            ]);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('student.login');
    }

    public function showRegister()
    {
        if (auth()->check() && auth()->user()->isStudent()) {
            return redirect()->route('student.dashboard');
        }
        return view('student.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'student',
        ]);

        Auth::login($user);
        $request->session()->flash('login_success', true);
        return redirect()->route('student.dashboard');
    }
}
