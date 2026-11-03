@extends('layouts.auth', [
    'title' => 'Admin Log In',
    'heading' => 'Welcome Back',
    'subheading' => 'Enter your account credentials.',
])

@section('content')
<span class="sr-only">TUTUT Admin Portal</span>
<div class="space-y-6">
    <flux:button icon="google" href="{{ route('auth.google') }}" class="w-full">
        Continue with Google
    </flux:button>

    <flux:separator text="or" />

    <form
        method="POST"
        action="{{ route('admin.login.submit') }}"
        class="space-y-6"
        x-data="{
            submitting: false,
            email: '{{ old('email', '') }}',
            password: '',
            touched: { email: false, password: false },
            errors: { email: '', password: '' },
            init() {
                this.$watch('email', () => { if (this.touched.email) this.validateEmail(); });
                this.$watch('password', () => { if (this.touched.password) this.validatePassword(); });
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
                } else {
                    this.errors.password = '';
                }
            },
            handleSubmit(e) {
                this.touched.email = true;
                this.touched.password = true;
                this.validateEmail();
                this.validatePassword();

                if (this.errors.email || this.errors.password) {
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
                @blur="touched.email = true; validateEmail()"
                value="{{ old('email') }}"
                placeholder="john.doe@example.com"
                required
                autofocus
                autocomplete="email"
            />
            <p x-show="errors.email" x-cloak class="text-xs font-medium text-rose-600 dark:text-rose-400 mt-1.5" role="alert" x-text="errors.email"></p>
            <flux:error name="email" />
        </flux:field>

        <flux:field>
            <flux:label>Password</flux:label>
            <flux:input
                type="password"
                name="password"
                x-model="password"
                @blur="touched.password = true; validatePassword()"
                placeholder="••••••••"
                viewable
                required
                autocomplete="current-password"
            />
            <p x-show="errors.password" x-cloak class="text-xs font-medium text-rose-600 dark:text-rose-400 mt-1.5" role="alert" x-text="errors.password"></p>
            <flux:error name="password" />
        </flux:field>

        <flux:checkbox name="remember" label="Remember me" />

        <flux:button variant="primary" type="submit" class="w-full font-semibold" ::disabled="submitting">
            <span x-show="!submitting">Log In</span>
            <span x-show="submitting" x-cloak class="inline-flex items-center gap-2">
                <flux:icon.loading class="size-4" />
                <span>Authenticating...</span>
            </span>
        </flux:button>
    </form>
</div>
@endsection
