@extends('layouts.auth', [
    'title' => 'Candidate Registration',
    'heading' => 'Register Candidate',
    'subheading' => 'Create your student account for official TOEFL examination',
])

@section('content')
<form method="POST" action="{{ route('student.register.submit') }}" class="space-y-4" aria-label="Student registration form">
    @csrf

    <flux:field>
        <flux:label>Full Name *</flux:label>
        <flux:input name="name" value="{{ old('name') }}" required autofocus placeholder="Ahmad Mahasiswa" icon="user" />
        <flux:error name="name" />
    </flux:field>

    <flux:field>
        <flux:label>Email Address *</flux:label>
        <flux:input type="email" name="email" value="{{ old('email') }}" required placeholder="student@university.edu" icon="envelope" />
        <flux:error name="email" />
    </flux:field>

    <flux:field>
        <flux:label>Password * (Minimum 8 characters)</flux:label>
        <flux:input type="password" name="password" required placeholder="••••••••" icon="key" />
        <flux:error name="password" />
    </flux:field>

    <flux:field>
        <flux:label>Confirm Password *</flux:label>
        <flux:input type="password" name="password_confirmation" required placeholder="••••••••" icon="key" />
        <flux:error name="password_confirmation" />
    </flux:field>

    <flux:button type="submit" variant="primary" class="w-full font-bold">
        Complete Registration
    </flux:button>

    <div class="text-center text-xs text-zinc-600 dark:text-zinc-400 pt-1">
        Already registered? <a href="{{ route('student.login') }}" class="font-bold text-blue-600 dark:text-blue-400 hover:underline">Log in here</a>
    </div>
</form>
@endsection
