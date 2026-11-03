@extends('layouts.auth', [
    'title' => 'Admin Sign In',
    'heading' => 'TUTUT Admin Portal',
    'subheading' => 'Administrative and proctor access only.',
])

@section('content')
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
        Sign in
    </flux:button>
</form>

<flux:separator />

<flux:text size="xs" class="text-center">
    <flux:link href="{{ route('student.login') }}" wire:navigate.hover>&larr; Candidate Portal</flux:link>
</flux:text>
@endsection
