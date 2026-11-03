@extends('layouts.auth', [
    'title' => 'TUTUT Admin Portal',
    'heading' => 'Welcome Back',
    'subheading' => 'Enter your details to continue.',
])

@section('content')
<span class="sr-only">TUTUT Admin Portal</span>
<div class="space-y-6">
    <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-6">
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

        <flux:button variant="primary" type="submit" class="w-full font-semibold">
            Log In
        </flux:button>
    </form>
</div>
@endsection
