@extends('layouts.auth', [
    'title' => 'Admin Sign In',
    'heading' => 'TUTUT Admin Portal',
    'subheading' => 'Restricted access for proctors and staff.',
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
            placeholder="admin@university.edu"
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

        <flux:button variant="primary" type="submit" class="w-full font-semibold">
            Sign In
        </flux:button>
    </form>
</div>
@endsection

@section('footer')
<flux:text size="xs" class="text-zinc-500 dark:text-zinc-400">Authorized Personnel Only</flux:text>

<flux:card.actions>
    <flux:link href="{{ route('student.login') }}" wire:navigate.hover class="font-medium text-xs">&larr; Candidate Portal</flux:link>
</flux:card.actions>
@endsection
