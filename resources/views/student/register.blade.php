@extends('layouts.auth', [
    'title' => 'Register',
    'heading' => 'Create Account',
    'subheading' => 'Register to participate in scheduled examinations.',
])

@section('content')
<flux:heading level="2" id="form-heading" class="sr-only">Candidate Registration</flux:heading>

<form method="POST" action="{{ route('student.register.submit') }}" class="space-y-4" aria-labelledby="form-heading">
    @csrf

    <flux:field>
        <flux:label>Full Name</flux:label>
        <flux:input name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="John Doe" icon="user" />
        <flux:error name="name" />
    </flux:field>

    <flux:field>
        <flux:label>Email</flux:label>
        <flux:input type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="name@example.com" icon="envelope" />
        <flux:error name="email" />
    </flux:field>

    <flux:field>
        <flux:label>Password</flux:label>
        <flux:input type="password" name="password" required autocomplete="new-password" placeholder="••••••••" icon="key" />
        <flux:error name="password" />
    </flux:field>

    <flux:field>
        <flux:label>Confirm Password</flux:label>
        <flux:input type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" icon="key" />
        <flux:error name="password_confirmation" />
    </flux:field>

    <flux:button type="submit" variant="primary" class="w-full font-semibold transition-all duration-200">
        Create Account
    </flux:button>

    <flux:separator />

    <flux:text size="sm" class="text-center text-zinc-500 dark:text-zinc-400">
        Already registered? <flux:link href="{{ route('student.login') }}" class="font-medium text-blue-600 dark:text-blue-400">Sign In</flux:link>
    </flux:text>
</form>
@endsection
