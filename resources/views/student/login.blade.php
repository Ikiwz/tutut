@extends('layouts.auth', [
    'title' => 'Candidate Sign In',
    'heading' => 'Candidate Sign In',
    'subheading' => 'Access your scheduled examination session.',
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
            placeholder="candidate@university.edu"
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
            Sign In to Exam
        </flux:button>
    </form>
</div>
@endsection

@section('footer')
<flux:text size="xs">
    Don't have an account? <flux:link href="{{ route('student.register') }}" wire:navigate.hover class="font-medium">Register</flux:link>
</flux:text>

<flux:card.actions>
    <flux:link href="{{ route('admin.login') }}" wire:navigate.hover class="font-medium text-xs">Admin Portal &rarr;</flux:link>
</flux:card.actions>
@endsection
