@extends('layouts.auth', [
    'title' => 'Student Register',
    'heading' => 'Create Account',
    'subheading' => 'Fill in your details to get started.',
])

@section('content')
<div class="space-y-4">
    <flux:button icon="google" href="{{ route('auth.google') }}" class="w-full">
        Continue with Google
    </flux:button>

    <flux:separator text="or" />

    <form
        method="POST"
        action="{{ route('student.register.submit') }}"
        class="space-y-4"
        x-data="{
            submitting: false,
            name: '{{ old('name', '') }}',
            email: '{{ old('email', '') }}',
            password: '',
            password_confirmation: '',
            touched: { name: false, email: false, password: false, password_confirmation: false },
            errors: { name: '', email: '', password: '', password_confirmation: '' },
            init() {
                this.$watch('name', () => { if (this.touched.name) this.validateName(); });
                this.$watch('email', () => { if (this.touched.email) this.validateEmail(); });
                this.$watch('password', () => {
                    if (this.touched.password) this.validatePassword();
                    if (this.touched.password_confirmation) this.validateConfirm();
                });
                this.$watch('password_confirmation', () => { if (this.touched.password_confirmation) this.validateConfirm(); });
            },
            validateName() {
                if (!this.name.trim()) {
                    this.errors.name = 'Full name is required.';
                } else if (this.name.trim().length < 3) {
                    this.errors.name = 'Name must be at least 3 characters.';
                } else {
                    this.errors.name = '';
                }
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
                    this.errors.password = 'Password must be at least 8 characters.';
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
                this.touched.name = true;
                this.touched.email = true;
                this.touched.password = true;
                this.touched.password_confirmation = true;
                this.validateName();
                this.validateEmail();
                this.validatePassword();
                this.validateConfirm();

                if (this.errors.name || this.errors.email || this.errors.password || this.errors.password_confirmation) {
                    e.preventDefault();
                    return;
                }
                this.submitting = true;
            }
        }"
        @submit="handleSubmit($event)"
    >
        @csrf
        <input type="text" name="portal_hp" class="hidden" tabindex="-1" autocomplete="off" aria-hidden="true" value="" />

        <flux:field>
            <flux:label>Full Name</flux:label>
            <flux:input
                name="name"
                x-model="name"
                @blur="touched.name = true; validateName()"
                value="{{ old('name') }}"
                placeholder="John Doe"
                required
                autofocus
                autocomplete="name"
            />
            <p x-show="errors.name" x-cloak class="text-xs font-medium text-rose-600 dark:text-rose-400 mt-1.5" role="alert" x-text="errors.name"></p>
            <flux:error name="name" />
        </flux:field>

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
                placeholder="At least 8 characters"
                viewable
                required
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
                placeholder="••••••••"
                viewable
                required
                autocomplete="new-password"
            />
            <p x-show="errors.password_confirmation" x-cloak class="text-xs font-medium text-rose-600 dark:text-rose-400 mt-1.5" role="alert" x-text="errors.password_confirmation"></p>
            <flux:error name="password_confirmation" />
        </flux:field>

        <flux:button variant="primary" type="submit" class="w-full font-semibold" ::disabled="submitting">
            <span x-show="!submitting">Create Account</span>
            <span x-show="submitting" x-cloak class="inline-flex items-center gap-2">
                <flux:icon.loading class="size-4" />
                <span>Creating Account...</span>
            </span>
        </flux:button>
    </form>
</div>
@endsection

@section('footer')
<div class="w-full text-center">
    <flux:text size="xs">
        Already have an account? <flux:link href="{{ route('student.login') }}" wire:navigate.hover class="font-medium">Log In</flux:link>
    </flux:text>
</div>
@endsection
