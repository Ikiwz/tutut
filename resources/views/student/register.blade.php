@extends('layouts.auth', [
    'title' => 'Candidate Registration',
    'heading' => 'Candidate Registration',
    'subheading' => 'Create your academic profile for campus TOEFL certification',
])

@section('content')
<h2 id="form-heading" class="sr-only">Candidate Registration Form</h2>

<form method="POST" action="{{ route('student.register.submit') }}" class="space-y-4" aria-label="Candidate registration form">
    @csrf

    <flux:field>
        <flux:label id="label-name">Full Legal Name *</flux:label>
        <flux:input name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Johnathan Doe" icon="user" aria-labelledby="label-name" />
        <flux:error name="name" />
    </flux:field>

    <flux:field>
        <flux:label id="label-email">Campus or Personal Email Address *</flux:label>
        <flux:input type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="student@university.edu" icon="envelope" aria-labelledby="label-email" />
        <flux:error name="email" />
    </flux:field>

    <flux:field>
        <flux:label id="label-password">Account Password * (Minimum 8 characters)</flux:label>
        <flux:input type="password" name="password" required autocomplete="new-password" placeholder="••••••••" icon="key" aria-labelledby="label-password" />
        <flux:error name="password" />
    </flux:field>

    <flux:field>
        <flux:label id="label-password-confirmation">Confirm Password *</flux:label>
        <flux:input type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" icon="key" aria-labelledby="label-password-confirmation" />
        <flux:error name="password_confirmation" />
    </flux:field>

    <flux:button type="submit" variant="primary" class="w-full font-bold shadow-md">
        Create Candidate Account
    </flux:button>

    <div class="text-center text-xs text-zinc-600 dark:text-zinc-400 pt-2 border-t border-zinc-100 dark:border-zinc-800">
        Already registered? <a href="{{ route('student.login') }}" class="font-bold text-blue-600 dark:text-blue-400 hover:underline">Sign in to existing account</a>
    </div>
</form>
@endsection
