@extends('layouts.auth', [
    'title' => 'Candidate Login',
    'heading' => 'Candidate Login',
    'subheading' => 'Sign in to access your scheduled TOEFL examination session',
])

@section('content')
<h2 id="form-heading" class="sr-only">Candidate Login Form</h2>

<form method="POST" action="{{ route('student.login.submit') }}" class="space-y-5" aria-label="Candidate login form">
    @csrf

    <flux:field>
        <flux:label id="label-email">Registered Email Address</flux:label>
        <flux:input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="name@university.edu" icon="envelope" aria-labelledby="label-email" />
        <flux:error name="email" />
    </flux:field>

    <flux:field>
        <flux:label id="label-password">Password</flux:label>
        <flux:input type="password" name="password" required autocomplete="current-password" placeholder="••••••••" icon="key" aria-labelledby="label-password" />
        <flux:error name="password" />
    </flux:field>

    <div class="flex items-center justify-between text-xs">
        <flux:checkbox name="remember" label="Remember this device" />
        <span class="text-zinc-500 dark:text-zinc-400">Shortcut: <kbd class="px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 font-mono text-[11px] border border-zinc-200 dark:border-zinc-700">Enter</kbd></span>
    </div>

    <flux:button type="submit" variant="primary" class="w-full font-bold shadow-md">
        Sign In to Exam Session
    </flux:button>

    <div class="text-center text-xs text-zinc-600 dark:text-zinc-400 pt-2 border-t border-zinc-100 dark:border-zinc-800">
        New candidate? <a href="{{ route('student.register') }}" class="font-bold text-blue-600 dark:text-blue-400 hover:underline">Register your account</a>
    </div>
</form>
@endsection
