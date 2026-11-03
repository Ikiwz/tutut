@extends('layouts.auth', [
    'title' => 'Candidate Registration',
    'heading' => 'Candidate Registration',
    'subheading' => 'Create your academic profile for campus TOEFL certification',
])

@section('content')
<flux:heading level="2" id="form-heading" class="sr-only">Candidate Registration Form</flux:heading>

<form method="POST" action="{{ route('student.register.submit') }}" class="space-y-4" aria-labelledby="form-heading">
    @csrf

    <flux:field>
        <flux:label>Full Legal Name *</flux:label>
        <flux:input name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Johnathan Doe" icon="user" />
        <flux:error name="name" />
    </flux:field>

    <flux:field>
        <flux:label>Campus or Personal Email Address *</flux:label>
        <flux:input type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="student@university.edu" icon="envelope" />
        <flux:error name="email" />
    </flux:field>

    <flux:field>
        <flux:label>Account Password * (Minimum 8 characters)</flux:label>
        <flux:input type="password" name="password" required autocomplete="new-password" placeholder="••••••••" icon="key" />
        <flux:error name="password" />
    </flux:field>

    <flux:field>
        <flux:label>Confirm Password *</flux:label>
        <flux:input type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" icon="key" />
        <flux:error name="password_confirmation" />
    </flux:field>

    <flux:button type="submit" variant="primary" class="w-full font-bold shadow-md">
        Create Candidate Account
    </flux:button>

    <flux:separator />

    <flux:text size="sm" class="text-center">
        Already registered? <flux:link href="{{ route('student.login') }}" class="font-bold">Sign in to existing account</flux:link>
    </flux:text>
</form>
@endsection
