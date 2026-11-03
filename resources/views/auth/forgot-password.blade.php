@extends('layouts.auth', [
    'title' => 'Forgot Password',
    'heading' => 'Forgot Password',
    'subheading' => 'Enter your email to receive a password reset link.',
])

@section('content')
<div class="space-y-6">
    <form
        method="POST"
        action="{{ route('password.email') }}"
        class="space-y-6"
        x-data="{
            submitting: false,
            email: '{{ old('email', '') }}',
            touched: false,
            error: '',
            init() {
                this.$watch('email', () => { if (this.touched) this.validate(); });
            },
            validate() {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!this.email.trim()) {
                    this.error = 'Email address is required.';
                } else if (!re.test(this.email.trim())) {
                    this.error = 'Please enter a valid email address.';
                } else {
                    this.error = '';
                }
            },
            handleSubmit(e) {
                this.touched = true;
                this.validate();
                if (this.error) {
                    e.preventDefault();
                    return;
                }
                this.submitting = true;
            }
        }"
        @submit="handleSubmit($event)"
    >
        @csrf

        <flux:field>
            <flux:label>Email Address</flux:label>
            <flux:input
                type="email"
                name="email"
                x-model="email"
                @blur="touched = true; validate()"
                value="{{ old('email') }}"
                placeholder="name@example.com"
                required
                autofocus
                autocomplete="email"
            />
            <p x-show="error" x-cloak class="text-xs font-medium text-rose-600 dark:text-rose-400 mt-1.5" role="alert" x-text="error"></p>
            <flux:error name="email" />
        </flux:field>

        <flux:button variant="primary" type="submit" :loading="false" class="w-full font-semibold" ::disabled="submitting">
            <span x-show="!submitting">Send Reset Link</span>
            <span x-show="submitting" x-cloak class="inline-flex items-center justify-center gap-2">
                <flux:icon.loading variant="micro" class="size-4" />
                <span>Sending Link...</span>
            </span>
        </flux:button>
    </form>
</div>
@endsection

@section('footer')
<div class="w-full text-center">
    <flux:text size="xs">
        Remember your password? <flux:link href="{{ route('student.login') }}" wire:navigate.hover class="font-medium">Log In</flux:link>
    </flux:text>
</div>
@endsection

