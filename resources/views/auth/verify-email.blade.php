@extends('layouts.auth', [
    'title' => 'Verify Email',
    'heading' => 'Verify Your Email',
    'subheading' => 'Please confirm your email address to activate your account.',
])

@section('content')
<div class="space-y-5">
    @if (session('status') == 'verification-link-sent')
        <flux:callout variant="success" icon="check-circle">
            A new verification link has been sent to <strong>{{ auth()->user()->email }}</strong>.
        </flux:callout>
    @else
        <flux:text size="sm" class="leading-relaxed">
            We sent a verification link to <strong class="text-zinc-900 dark:text-white">{{ auth()->user()->email }}</strong>. Please check your inbox and click the link to verify your account before taking your examination.
        </flux:text>
    @endif

    <div class="p-3.5 rounded-xl bg-zinc-100 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700/60 text-xs text-zinc-600 dark:text-zinc-300 space-y-1.5">
        <div class="flex items-center gap-2 font-medium text-zinc-800 dark:text-zinc-200">
            <flux:icon icon="information-circle" class="size-4 text-blue-600 dark:text-blue-400" />
            <span>Didn't receive the email?</span>
        </div>
        <p class="ps-6 text-zinc-500 dark:text-zinc-400">
            Check your spam or junk folder. If it is still missing, click the button below to request a new link.
        </p>
    </div>

    <form
        method="POST"
        action="{{ route('verification.send') }}"
        x-data="{ submitting: false }"
        @submit="submitting = true"
    >
        @csrf
        <flux:button variant="primary" type="submit" :loading="false" class="w-full font-semibold" ::disabled="submitting">
            <span x-show="!submitting">Resend Verification Email</span>
            <span x-show="submitting" x-cloak class="inline-flex items-center justify-center gap-2">
                <flux:icon.loading variant="micro" class="size-4" />
                <span>Resending Email...</span>
            </span>
        </flux:button>
    </form>
</div>
@endsection

@section('footer')
<div class="w-full flex items-center justify-between text-xs">
    <flux:text size="xs">
        Need to switch accounts?
    </flux:text>
    <form method="POST" action="{{ route('student.logout') }}">
        @csrf
        <flux:button type="submit" variant="ghost" size="xs" class="font-medium text-rose-600 dark:text-rose-400 hover:text-rose-700">
            Log Out
        </flux:button>
    </form>
</div>
@endsection
