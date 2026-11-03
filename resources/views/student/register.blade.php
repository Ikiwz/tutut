@extends('layouts.auth', [
    'title' => 'Register',
    'heading' => 'Welcome',
    'subheading' => 'Enter your details to get started.',
    'bodyClass' => 'space-y-3',
])

@section('content')
<div class="space-y-3">
    <flux:button icon="google" href="{{ route('auth.google') }}" class="w-full">
        Continue with Google
    </flux:button>

    <flux:separator text="or" />

    <form
        method="POST"
        action="{{ route('student.register.submit') }}"
        class="space-y-3"
        x-data="{
            submitting: false,
            name: '{{ old('name', '') }}',
            email: '{{ old('email', '') }}',
            password: '',
            password_confirmation: '',
            touched: { name: false, email: false, password: false, password_confirmation: false },
            errors: { name: '', email: '', password: '', password_confirmation: '' },
            get passwordScore() {
                if (!this.password) return 0;
                if (this.password.length < 8) return 1;
                const hasLetters = /[a-zA-Z]/.test(this.password);
                const hasNumbers = /[0-9]/.test(this.password);
                const hasVariety = /[^a-zA-Z0-9]/.test(this.password) || (/[a-z]/.test(this.password) && /[A-Z]/.test(this.password));
                if ((hasLetters && hasNumbers && this.password.length >= 10) || (hasLetters && hasNumbers && hasVariety)) {
                    return 3;
                }
                return 2;
            },
            get passwordPercentage() {
                switch (this.passwordScore) {
                    case 1: return 33;
                    case 2: return 66;
                    case 3: return 100;
                    default: return 0;
                }
            },
            get passwordStrengthLabel() {
                switch (this.passwordScore) {
                    case 1: return 'Weak';
                    case 2: return 'Good';
                    case 3: return 'Strong';
                    default: return '';
                }
            },
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
                } else if (this.name.trim().length < 2) {
                    this.errors.name = 'Full name must be at least 2 characters.';
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
                placeholder="Full name"
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
                placeholder="name@example.com"
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
            <div x-show="password.length > 0 && !errors.password" x-cloak class="space-y-1.5 mt-2" aria-live="polite">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-zinc-500 dark:text-zinc-400">Password strength</span>
                    <span class="font-medium" :class="{
                        'text-rose-600 dark:text-rose-400': passwordScore === 1,
                        'text-amber-600 dark:text-amber-400': passwordScore === 2,
                        'text-emerald-600 dark:text-emerald-400': passwordScore === 3
                    }" x-text="passwordStrengthLabel"></span>
                </div>
                <flux:progress
                    class="h-1.5"
                    x-effect="
                        let color = passwordScore === 1 ? 'var(--color-rose-500)' : (passwordScore === 2 ? 'var(--color-amber-500)' : 'var(--color-emerald-500)');
                        $el.style.setProperty('--flux-progress-color', color);
                        $el.style.setProperty('--flux-progress-percentage', passwordPercentage + '%');
                        $el.setAttribute('aria-valuenow', passwordPercentage);
                    "
                    aria-label="Password strength"
                />
            </div>
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
            <span x-show="!submitting">Register</span>
            <span x-show="submitting" x-cloak class="inline-flex items-center justify-center gap-2">
                <flux:icon.loading variant="micro" class="size-4" />
                <span>Registering...</span>
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
