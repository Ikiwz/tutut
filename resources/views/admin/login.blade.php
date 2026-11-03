@extends('layouts.auth', [
    'title' => 'Admin Sign In',
    'heading' => 'TUTUT Admin Portal',
    'subheading' => 'Administrative and proctor access only.',
])

@section('content')
<div class="space-y-6">
    <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-6">
        @csrf

        <flux:input
            label="Email"
            type="email"
            name="email"
            value="{{ old('email') }}"
            placeholder="admin@example.com"
            icon="envelope"
            required
            autofocus
            autocomplete="email"
        />

        <flux:input
            label="Password"
            type="password"
            name="password"
            placeholder="••••••••"
            icon="key"
            viewable
            required
            autocomplete="current-password"
        />

        <flux:checkbox name="remember" label="Remember me" />

        <flux:button variant="primary" type="submit" class="w-full">
            Sign In
        </flux:button>
    </form>

    <flux:separator />

    <div class="flex items-center justify-between text-xs">
        <flux:text size="xs" class="text-zinc-500">Authorized Personnel Only</flux:text>
        <flux:link href="{{ route('student.login') }}" wire:navigate.hover>&larr; Candidate Portal</flux:link>
    </div>
</div>
@endsection
