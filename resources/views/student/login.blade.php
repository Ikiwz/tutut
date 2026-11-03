@extends('layouts.auth', [
    'title' => 'Candidate Login',
    'heading' => 'Candidate Login',
    'subheading' => 'Sign in to access your scheduled TOEFL examination session',
])

@section('content')
<flux:heading level="2" id="form-heading" class="sr-only">Candidate Login Form</flux:heading>

<form method="POST" action="{{ route('student.login.submit') }}" class="space-y-4" aria-labelledby="form-heading">
    @csrf

    <flux:field>
        <flux:label>Registered Email Address</flux:label>
        <flux:input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="candidate@university.edu" icon="envelope" />
        <flux:error name="email" />
    </flux:field>

    <flux:field>
        <flux:label>Password</flux:label>
        <flux:input type="password" name="password" required autocomplete="current-password" placeholder="••••••••" icon="key" />
        <flux:error name="password" />
    </flux:field>

    <div class="flex items-center justify-between text-xs">
        <flux:checkbox name="remember" label="Remember this device" />
        <flux:text size="xs" class="text-zinc-500 dark:text-zinc-400">
            Shortcut: <kbd class="px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 font-mono text-[11px] border border-zinc-200 dark:border-zinc-700">Enter</kbd>
        </flux:text>
    </div>

    <flux:button type="submit" variant="primary" class="w-full font-bold shadow-md">
        Sign In to Exam Session
    </flux:button>

    <flux:separator />

    <flux:text size="sm" class="text-center">
        New candidate? <flux:link href="{{ route('student.register') }}" class="font-bold">Register your account</flux:link>
    </flux:text>
</form>
@endsection
