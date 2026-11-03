@extends('layouts.auth', [
    'title' => 'Student Log In',
    'heading' => 'Welcome Back',
    'subheading' => 'Enter your account credentials.',
])

@section('content')
<div class="space-y-6">
    <flux:button icon="google" href="{{ route('auth.google') }}" class="w-full">
        Continue with Google
    </flux:button>

    <flux:separator text="or" />

    <form method="POST" action="{{ route('student.login.submit') }}" class="space-y-6" x-data="{ submitting: false }" @submit="submitting = true">
        @csrf

        <flux:field>
            <flux:label>Email Address</flux:label>
            <flux:input
                type="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="john.doe@example.com"
                required
                autofocus
                autocomplete="email"
            />
            <flux:error name="email" />
        </flux:field>

        <flux:field>
            <flux:label>Password</flux:label>
            <flux:input
                type="password"
                name="password"
                placeholder="••••••••"
                viewable
                required
                autocomplete="current-password"
            />
            <flux:error name="password" />
        </flux:field>

        <flux:checkbox name="remember" label="Remember me" />

        <flux:button variant="primary" type="submit" class="w-full font-semibold" ::disabled="submitting">
            <span x-show="!submitting">Log In</span>
            <span x-show="submitting" x-cloak class="inline-flex items-center gap-2">
                <flux:icon.loading class="size-4" />
                <span>Logging In...</span>
            </span>
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
