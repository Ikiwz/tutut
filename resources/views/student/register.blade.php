@extends('layouts.auth', [
    'title' => 'Register',
    'heading' => 'Welcome',
    'subheading' => 'Enter your account details.',
])

@section('content')
<div class="space-y-6">
    <flux:button icon="google" href="{{ route('auth.google') }}" class="w-full">
        Continue with Google
    </flux:button>

    <flux:separator text="or" />

    <form method="POST" action="{{ route('student.register.submit') }}" class="space-y-6" x-data="{ submitting: false }" @submit="submitting = true">
        @csrf

        <flux:input
            label="Full Legal Name"
            name="name"
            value="{{ old('name') }}"
            placeholder="John Doe"
            icon="user"
            required
            autofocus
            autocomplete="name"
        />

        <flux:input
            label="Email Address"
            type="email"
            name="email"
            value="{{ old('email') }}"
            placeholder="john.doe@example.com"
            icon="envelope"
            required
            autocomplete="email"
        />

        <flux:input
            label="Password"
            description:trailing="Minimum 8 characters with letters and numbers."
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

        <flux:button variant="primary" type="submit" class="w-full font-semibold" ::disabled="submitting">
            <span x-show="!submitting" class="inline-flex items-center gap-2">
                <flux:icon name="user-plus" variant="mini" class="size-4" />
                <span>Register</span>
            </span>
            <span x-show="submitting" x-cloak class="inline-flex items-center gap-2">
                <flux:icon.loading class="size-4" />
                <span>Creating Account...</span>
            </span>
        </flux:button>
    </form>
</div>
@endsection

@section('footer')
<div class="w-full text-center">
    <flux:text size="xs">
        Already have an account? <flux:link href="{{ route('student.login') }}" wire:navigate.hover class="font-medium">Log In</flux:link>
    </flux:text>
</div>
@endsection
