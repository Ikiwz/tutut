@extends('layouts.auth', [
    'title' => 'Student Register',
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

        <flux:field>
            <flux:label>Full Legal Name</flux:label>
            <flux:input
                name="name"
                value="{{ old('name') }}"
                placeholder="John Doe"
                required
                autofocus
                autocomplete="name"
            />
            <flux:error name="name" />
        </flux:field>

        <flux:field>
            <flux:label>Email Address</flux:label>
            <flux:input
                type="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="john.doe@example.com"
                required
                autocomplete="email"
            />
            <flux:error name="email" />
        </flux:field>

        <flux:field>
            <flux:label>Password</flux:label>
            <flux:description>Minimum 8 characters with letters and numbers.</flux:description>
            <flux:input
                type="password"
                name="password"
                placeholder="••••••••"
                viewable
                required
                autocomplete="new-password"
            />
            <flux:error name="password" />
        </flux:field>

        <flux:field>
            <flux:label>Confirm Password</flux:label>
            <flux:input
                type="password"
                name="password_confirmation"
                placeholder="••••••••"
                viewable
                required
                autocomplete="new-password"
            />
            <flux:error name="password_confirmation" />
        </flux:field>

        <flux:button variant="primary" type="submit" class="w-full font-semibold" ::disabled="submitting">
            <span x-show="!submitting">Register</span>
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
