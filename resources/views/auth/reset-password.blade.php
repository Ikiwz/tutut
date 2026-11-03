@extends('layouts.auth', [
    'title' => 'Set New Password',
    'heading' => 'Set New Password',
    'subheading' => 'Choose a secure password for your account.',
])

@section('content')
<div class="space-y-6">
    <form
        method="POST"
        action="{{ route('password.update') }}"
        class="space-y-6"
        x-data="{
            submitting: false,
            email: '{{ old('email', $email ?? '') }}',
            password: '',
            password_confirmation: '',
            touched: { email: false, password: false, password_confirmation: false },
            errors: { email: '', password: '', password_confirmation: '' },
            init() {
                this.$watch('email', () => { if (this.touched.email) this.validateEmail(); });
                this.$watch('password', () => {
                    if (this.touched.password) this.validatePassword();
                    if (this.touched.password_confirmation) this.validateConfirm();
                });
                this.$watch('password_confirmation', () => { if (this.touched.password_confirmation) this.validateConfirm(); });
            },
            validateEmail() {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!this.email.trim()) {
                    this.errors.email = 'Email address is required.';
                } else if (!re.test(this.email.trim())) {
                    this.errors.email = 'Please enter a valid email address.';
                } else {
                    this.errors.email = '';
                }
            },
            validatePassword() {
                if (!this.password) {
                    this.errors.password = 'Password is required.';
                } else if (this.password.length < 8) {
                    this.errors.password = 'Password must be at least 8 characters long.';
                } else {
                    this.errors.password = '';
                }
            },
            validateConfirm() {
                if (!this.password_confirmation) {
                    this.errors.password_confirmation = 'Please confirm your password.';
                } else if (this.password_confirmation !== this.password) {
                    this.errors.password_confirmation = 'Passwords do not match.';
                } else {
                    this.errors.password_confirmation = '';
                }
            },
            handleSubmit(e) {
                this.touched.email = true;
                this.touched.password = true;
                this.touched.password_confirmation = true;
                this.validateEmail();
                this.validatePassword();
                this.validateConfirm();

                if (this.errors.email || this.errors.password || this.errors.password_confirmation) {
                    e.preventDefault();
                    return;
                }
                this.submitting = true;
            }
        }"
        @submit="handleSubmit($event)"
    >
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <flux:field>
            <flux:label>Email Address</flux:label>
            <flux:input
                type="email"
                name="email"
                x-model="email"
                @blur="touched.email = true; validateEmail()"
                value="{{ old('email', $email ?? '') }}"
                placeholder="name@example.com"
                required
                autocomplete="email"
            />
            <p x-show="errors.email" x-cloak class="text-xs font-medium text-rose-600 dark:text-rose-400 mt-1.5" role="alert" x-text="errors.email"></p>
            <flux:error name="email" />
        </flux:field>

        <flux:field>
            <flux:label>New Password</flux:label>
            <flux:input
                type="password"
                name="password"
                x-model="password"
                @blur="touched.password = true; validatePassword()"
                placeholder="At least 8 characters"
                viewable
                required
                autofocus
                autocomplete="new-password"
            />
            <p x-show="errors.password" x-cloak class="text-xs font-medium text-rose-600 dark:text-rose-400 mt-1.5" role="alert" x-text="errors.password"></p>
            <flux:error name="password" />
        </flux:field>

        <flux:field>
            <flux:label>Confirm Password</flux:label>
            <flux:input
                type="password"
                name="password_confirmation"
                x-model="password_confirmation"
                @blur="touched.password_confirmation = true; validateConfirm()"
                placeholder="Repeat password"
                viewable
                required
                autocomplete="new-password"
            />
            <p x-show="errors.password_confirmation" x-cloak class="text-xs font-medium text-rose-600 dark:text-rose-400 mt-1.5" role="alert" x-text="errors.password_confirmation"></p>
            <flux:error name="password_confirmation" />
        </flux:field>

        <flux:button variant="primary" type="submit" :loading="false" class="w-full font-semibold" ::disabled="submitting">
            <span x-show="!submitting">Reset Password</span>
            <span x-show="submitting" x-cloak class="inline-flex items-center justify-center gap-2">
                <flux:icon.loading variant="micro" class="size-4" />
                <span>Resetting Password...</span>
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

