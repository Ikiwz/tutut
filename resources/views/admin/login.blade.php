@extends('layouts.auth', [
    'title' => 'Admin Login',
    'heading' => 'TUTUT Admin Portal',
    'subheading' => 'Sign in with campus proctor or administrator credentials',
])

@section('content')
<form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-4" aria-label="Administrator login form">
    @csrf

    <flux:field>
        <flux:label>Administrator Email</flux:label>
        <flux:input type="email" name="email" value="{{ old('email', 'admin@toefl.com') }}" required autofocus placeholder="admin@toefl.com" icon="envelope" />
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
        Sign In to Admin Dashboard
    </flux:button>

    <div class="p-3 rounded-xl bg-zinc-100 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700 text-xs text-zinc-700 dark:text-zinc-300 space-y-1">
        <div class="font-bold flex items-center gap-1.5">
            <flux:icon icon="shield-check" class="size-4 text-emerald-600 dark:text-emerald-400" />
            <span>Default Administrator Credentials:</span>
        </div>
        <div class="font-mono text-[11px]">Email: <strong>admin@toefl.com</strong> | Password: <strong>password</strong></div>
    </div>
</form>
@endsection
