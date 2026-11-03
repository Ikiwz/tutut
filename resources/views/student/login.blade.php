@extends('layouts.auth', [
    'title' => 'Log In',
    'heading' => 'Welcome Back',
    'subheading' => 'Enter your details to continue.',
])

@section('content')
<div class="space-y-6">
    <flux:button icon="google" href="{{ route('auth.google') }}" class="w-full">
        Continue with Google
    </flux:button>

    <flux:separator text="or" />

    <form method="POST" action="{{ route('student.login.submit') }}" class="space-y-6">
        @csrf

        <flux:input
            label="Email Address"
            type="email"
            name="email"
            value="{{ old('email') }}"
            placeholder="name@example.com"
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

        <flux:checkbox name="remember" label="Remember this device" />

        <flux:button variant="primary" type="submit" class="w-full font-semibold">
            Log In
        </flux:button>
    </form>
</div>
@endsection

@section('footer')
<div class="w-full text-center">
    <flux:text size="xs">
        Don't have an account? <flux:link href="{{ route('student.register') }}" wire:navigate.hover class="font-medium">Register</flux:link>
    </flux:text>
</div>
@endsection
