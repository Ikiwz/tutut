@extends('layouts.auth', [
    'title' => 'Candidate Login',
    'heading' => 'Candidate Login',
    'subheading' => 'Sign in to access your scheduled TOEFL exam session',
])

@section('content')
<form method="POST" action="{{ route('student.login.submit') }}" class="space-y-4" aria-label="Student login form">
    @csrf

    <flux:field>
        <flux:label>Student Email Address</flux:label>
        <flux:input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="student@toefl.com" icon="envelope" />
        <flux:error name="email" />
    </flux:field>

    <flux:field>
        <flux:label>Password</flux:label>
        <flux:input type="password" name="password" required placeholder="••••••••" icon="key" />
        <flux:error name="password" />
    </flux:field>

    <div class="flex items-center justify-between text-xs">
        <flux:checkbox name="remember" label="Remember me" />
        <span class="text-zinc-500">Shortcut: <kbd class="px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 font-mono text-[10px]">Enter</kbd></span>
    </div>

    <flux:button type="submit" variant="primary" class="w-full font-bold">
        Sign In to Exam Portal
    </flux:button>

    <div class="p-3 rounded-xl bg-blue-50/80 dark:bg-blue-950/40 border border-blue-200/60 dark:border-blue-900/40 text-xs text-blue-900 dark:text-blue-200 space-y-1">
        <div class="font-bold flex items-center gap-1.5">
            <flux:icon icon="information-circle" class="size-4 text-blue-600 dark:text-blue-400" />
            <span>Demo Candidate Credentials:</span>
        </div>
        <div class="font-mono text-[11px]">Email: <strong>student@toefl.com</strong> | Password: <strong>password</strong></div>
    </div>

    <div class="text-center text-xs text-zinc-600 dark:text-zinc-400 pt-1">
        Don't have an account? <a href="{{ route('student.register') }}" class="font-bold text-blue-600 dark:text-blue-400 hover:underline">Register here</a>
    </div>
</form>
@endsection
