@extends('layouts.auth', [
    'title' => 'Admin Sign In',
    'heading' => 'TUTUT Admin Portal',
    'subheading' => 'Administrative and proctor access only.',
])

@section('content')
<flux:heading level="2" id="form-heading" class="sr-only">Administrator Sign In</flux:heading>

<form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-4" aria-labelledby="form-heading">
    @csrf

    <flux:field>
        <flux:label>Email</flux:label>
        <flux:input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="admin@example.com" icon="envelope" />
        <flux:error name="email" />
    </flux:field>

    <flux:field>
        <flux:label>Password</flux:label>
        <flux:input type="password" name="password" required autocomplete="current-password" placeholder="••••••••" icon="key" />
        <flux:error name="password" />
    </flux:field>

    <div class="flex items-center justify-between text-xs">
        <flux:checkbox name="remember" label="Remember me" />
        <flux:text size="xs" class="text-zinc-500 dark:text-zinc-400">
            Press <kbd class="px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 font-mono text-[11px] border border-zinc-200 dark:border-zinc-700">Enter</kbd>
        </flux:text>
    </div>

    <flux:button type="submit" variant="primary" class="w-full font-semibold transition-all duration-200">
        Sign In
    </flux:button>
</form>
@endsection
