@extends('layouts.auth', [
    'title' => 'Candidate Registration',
    'heading' => 'Candidate Registration',
    'subheading' => 'Register your official details to participate in TOEFL examinations.',
])

@section('content')
<div class="space-y-6">
    <flux:button icon="google" href="{{ route('auth.google') }}" class="w-full">
        Sign up with Google
    </flux:button>

    <flux:separator text="or fill out registration details" />

    <form method="POST" action="{{ route('student.register.submit') }}" class="space-y-6">
        @csrf

        <flux:input
            label="Full Legal Name"
            description:trailing="As printed on your Student ID / Passport for official score certificates."
            name="name"
            value="{{ old('name') }}"
            placeholder="e.g. Alexander Pierce"
            icon="user"
            required
            autofocus
            autocomplete="name"
        />

        <flux:input
            label="Active Email Address"
            description:trailing="Your exam confirmation and test score results will be sent here."
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
            Create Candidate Account
        </flux:button>
    </form>

    <flux:separator />

    <div class="flex items-center justify-between text-xs">
        <flux:text size="xs">
            Already registered? <flux:link href="{{ route('student.login') }}" wire:navigate.hover class="font-medium">Sign In</flux:link>
        </flux:text>

        <flux:link href="{{ route('admin.login') }}" wire:navigate.hover class="font-medium">Administrator Portal &rarr;</flux:link>
    </div>
</div>
@endsection
