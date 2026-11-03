@extends('layouts.auth', [
    'title' => 'Register',
    'heading' => 'Create Account',
    'subheading' => 'Register to participate in examinations.',
])

@section('content')
<div class="space-y-6">
    <flux:button icon="google" href="{{ route('auth.google') }}" class="w-full">
        Sign up with Google
    </flux:button>

    <flux:separator text="or" />

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
    </form>

    <flux:separator />

    <div class="flex items-center justify-between text-xs">
        <flux:text size="xs">
            Already registered? <flux:link href="{{ route('student.login') }}" wire:navigate.hover>Sign In</flux:link>
        </flux:text>

        <flux:link href="{{ route('admin.login') }}" wire:navigate.hover>Admin Portal &rarr;</flux:link>
    </div>
</div>
@endsection
