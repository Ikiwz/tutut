@extends('layouts.auth', [
    'title' => 'Admin Sign In',
    'heading' => 'TUTUT Admin Portal',
    'subheading' => 'Restricted access for proctors, examiners, and system administrators.',
])

@section('content')
<div class="space-y-6">
    <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-6">
        @csrf

        <flux:input
            label="Staff Email Address"
            type="email"
            name="email"
            value="{{ old('email') }}"
            placeholder="admin@university.edu"
            icon="envelope"
            required
            autofocus
            autocomplete="email"
        />

        <flux:input
            label="Security Password"
            type="password"
            name="password"
            placeholder="••••••••"
            icon="key"
            viewable
            required
            autocomplete="current-password"
        />

        <flux:checkbox name="remember" label="Remember credentials" />

        <flux:button variant="primary" type="submit" class="w-full font-semibold">
            Access Admin Console
        </flux:button>
    </form>

    <flux:separator />

    <div class="flex items-center justify-between text-xs">
        <flux:text size="xs" class="text-zinc-500 dark:text-zinc-400">Authorized Personnel Only</flux:text>
        <flux:link href="{{ route('student.login') }}" wire:navigate.hover class="font-medium">&larr; Candidate Portal</flux:link>
    </div>
</div>
@endsection
