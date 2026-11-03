@extends('layouts.auth', [
    'title' => 'Sign In',
    'heading' => 'Candidate Sign In',
    'subheading' => 'Enter your credentials to access your exam session.',
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
            label="Email"
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

        <flux:checkbox name="remember" label="Remember me" />

        <flux:button variant="primary" type="submit" class="w-full">
            Sign In
        </flux:button>
    </form>

    <flux:separator />

    <div class="flex items-center justify-between text-xs">
        <flux:text size="xs">
            Don't have an account? <flux:link href="{{ route('student.register') }}" wire:navigate.hover>Register</flux:link>
        </flux:text>

        <flux:link href="{{ route('admin.login') }}" wire:navigate.hover>Admin Portal &rarr;</flux:link>
    </div>
</div>
@endsection
