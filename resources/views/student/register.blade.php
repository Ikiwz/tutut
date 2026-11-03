@extends('layouts.student')
@section('title', 'Student Registration')

@section('content')
<div class="min-h-[calc(100vh-80px)] flex items-center justify-center p-4">
    <div class="w-full max-w-md space-y-6">
        <flux:card class="space-y-6 shadow-xl border border-zinc-200 dark:border-zinc-800">
            <div class="text-center">
                <img src="{{ asset('images/logo.png') }}" alt="TUTUT Logo" class="h-16 w-auto mx-auto mb-3" aria-hidden="true">
                <flux:heading size="xl" level="1">Register New Account</flux:heading>
                <flux:text class="mt-1 text-sm">Join the TUTUT Accessible TOEFL Examination Platform</flux:text>
            </div>

            @if ($errors->any())
                <flux:callout variant="danger" icon="exclamation-triangle">
                    {{ $errors->first() }}
                </flux:callout>
            @endif

            <form method="POST" action="{{ route('student.register.submit') }}" class="space-y-4" aria-label="Student registration form">
                @csrf

                <flux:field>
                    <flux:label>Full Name *</flux:label>
                    <flux:input name="name" value="{{ old('name') }}" required autofocus placeholder="John Doe" icon="user" />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>Email Address *</flux:label>
                    <flux:input type="email" name="email" value="{{ old('email') }}" required placeholder="student@university.edu" icon="envelope" />
                    <flux:error name="email" />
                </flux:field>

                <flux:field>
                    <flux:label>Password * (Min. 8 characters)</flux:label>
                    <flux:input type="password" name="password" required placeholder="••••••••" icon="key" />
                    <flux:error name="password" />
                </flux:field>

                <flux:field>
                    <flux:label>Confirm Password *</flux:label>
                    <flux:input type="password" name="password_confirmation" required placeholder="••••••••" icon="key" />
                    <flux:error name="password_confirmation" />
                </flux:field>

                <flux:button type="submit" variant="primary" class="w-full font-bold">
                    Create Student Account
                </flux:button>
            </form>

            <div class="text-center text-xs text-zinc-600 dark:text-zinc-400">
                Already registered? <a href="{{ route('student.login') }}" class="font-bold text-blue-600 dark:text-blue-400 hover:underline">Log in here</a>
            </div>
        </flux:card>

        <div class="text-center text-xs text-zinc-500">
            Press <kbd class="px-1.5 py-0.5 rounded bg-zinc-200 dark:bg-zinc-700 font-mono text-[11px]">H</kbd> for accessibility keyboard shortcuts
        </div>
    </div>
</div>
@endsection
