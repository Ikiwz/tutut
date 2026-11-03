@extends('layouts.auth', [
    'title' => 'Register',
    'heading' => 'Create Account',
    'subheading' => 'Register to participate in examinations.',
])

@section('content')
<form method="POST" action="{{ route('student.register.submit') }}" class="space-y-6">
    @csrf

    <flux:input
        label="Full Name"
        name="name"
        value="{{ old('name') }}"
        placeholder="John Doe"
        icon="user"
        required
        autofocus
        autocomplete="name"
    />

    <flux:input
        label="Email"
        type="email"
        name="email"
        value="{{ old('email') }}"
        placeholder="name@example.com"
        icon="envelope"
        required
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
        autocomplete="new-password"
    />

    <flux:input
        label="Confirm Password"
        type="password"
        name="password_confirmation"
        placeholder="••••••••"
        icon="key"
        viewable
        required
        autocomplete="new-password"
    />

    <flux:button variant="primary" type="submit" class="w-full">
        Register
    </flux:button>

    <flux:separator />

    <flux:text size="sm" class="text-center">
        Already registered? <flux:link href="{{ route('student.login') }}" wire:navigate.hover>Sign in</flux:link>
    </flux:text>
</form>
@endsection
