@extends('layouts.student')
@section('title', 'Student Login')

@section('content')
<div class="min-h-[calc(100vh-80px)] flex items-center justify-center p-4">
    <div class="w-full max-w-md space-y-6">
        <flux:card class="space-y-6 shadow-xl border border-zinc-200 dark:border-zinc-800">
            <div class="text-center">
                <img src="{{ asset('images/logo.png') }}" alt="TUTUT Logo" class="h-16 w-auto mx-auto mb-3" aria-hidden="true">
                <flux:heading size="xl" level="1">Log in to TUTUT</flux:heading>
                <flux:text class="mt-1 text-sm">Accessible TOEFL Examination Platform for Visually Impaired Students</flux:text>
            </div>

            @if ($errors->any())
                <flux:callout variant="danger" icon="exclamation-triangle">
                    {{ $errors->first() }}
                </flux:callout>
            @endif

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

                <flux:button type="submit" variant="primary" class="w-full font-bold">
                    Log In to Exam Portal
                </flux:button>
            </form>

            <div class="p-3.5 rounded-xl bg-blue-50/70 dark:bg-blue-950/40 border border-blue-200/60 dark:border-blue-900/40 text-xs text-blue-900 dark:text-blue-200 space-y-1">
                <div class="font-bold flex items-center gap-1.5">
                    <flux:icon icon="information-circle" class="size-4 text-blue-600 dark:text-blue-400" />
                    <span>Demo Student Credentials:</span>
                </div>
                <div class="font-mono">Email: <strong>student@toefl.com</strong> | Password: <strong>password</strong></div>
            </div>

            <div class="text-center text-xs text-zinc-600 dark:text-zinc-400">
                Don't have an account? <a href="{{ route('student.register') }}" class="font-bold text-blue-600 dark:text-blue-400 hover:underline">Register here</a>
            </div>
        </flux:card>

        <div class="text-center text-xs text-zinc-500">
            Press <kbd class="px-1.5 py-0.5 rounded bg-zinc-200 dark:bg-zinc-700 font-mono text-[11px]">H</kbd> for accessibility keyboard shortcuts
        </div>
    </div>
</div>
@endsection
