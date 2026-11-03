@extends('layouts.auth', [
    'title' => 'Admin Login',
    'heading' => 'TUTUT Admin Portal',
    'subheading' => 'Campus proctor and administrator management authentication',
])

@section('content')
<h2 id="form-heading" class="sr-only">Administrator Authentication Form</h2>

<form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-5" aria-label="Administrator login form">
    @csrf

    <flux:field>
        <flux:label id="label-admin-email">Administrator Email Address</flux:label>
        <flux:input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="proctor@university.edu" icon="envelope" aria-labelledby="label-admin-email" />
        <flux:error name="email" />
    </flux:field>

    <flux:field>
        <flux:label id="label-admin-password">Password</flux:label>
        <flux:input type="password" name="password" required autocomplete="current-password" placeholder="••••••••" icon="key" aria-labelledby="label-admin-password" />
        <flux:error name="password" />
    </flux:field>

    <div class="flex items-center justify-between text-xs">
        <flux:checkbox name="remember" label="Remember this administrative session" />
        <span class="text-zinc-500 dark:text-zinc-400">Shortcut: <kbd class="px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 font-mono text-[11px] border border-zinc-200 dark:border-zinc-700">Enter</kbd></span>
    </div>

    <flux:button type="submit" variant="primary" class="w-full font-bold shadow-md">
        Access Admin Dashboard
    </flux:button>
</form>
@endsection
