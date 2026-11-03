@extends('layouts.auth', [
    'title' => 'Register',
    'heading' => 'Create an Account',
    'subheading' => 'Register your details to participate in examinations.',
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
            label="Full Legal Name"
            description:trailing="Printed on your official score certificate."
            name="name"
            value="{{ old('name') }}"
            placeholder="e.g. Alexander Pierce"
            icon="user"
            required
            autofocus
            autocomplete="name"
        />

        <flux:input
            label="Email Address"
            description:trailing="Exam links and score results will be delivered here."
            type="email"
            name="email"
            value="{{ old('email') }}"
            placeholder="candidate@university.edu"
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

        <flux:button variant="primary" type="submit" class="w-full font-semibold">
            Register Account
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
