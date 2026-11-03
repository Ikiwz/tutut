@extends('layouts.auth', [
    'title' => 'Admin Log In',
    'heading' => 'Welcome Back',
    'subheading' => 'Enter your account credentials.',
])

@section('content')
<span class="sr-only">TUTUT Admin Portal</span>
<div class="space-y-6">
    <flux:button icon="google" href="{{ route('auth.google') }}" class="w-full">
        Continue with Google
    </flux:button>

    <flux:separator text="or" />

    <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-6">
        @csrf

        <flux:input
            label="Email Address"
            type="email"
            name="email"
            value="{{ old('email') }}"
            placeholder="john.doe@example.com"
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
            Log In
        </flux:button>
    </form>
</div>
@endsection
