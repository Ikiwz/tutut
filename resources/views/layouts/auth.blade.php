<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <title>{{ $title ?? 'Sign In' }} — TUTUT</title>
    <meta name="description" content="TUTUT Examination Platform.">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @fluxAppearance
</head>
<body class="min-h-dvh bg-zinc-50 dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 flex flex-col items-center justify-center p-4 sm:p-6 antialiased selection:bg-blue-600 selection:text-white transition-colors duration-200">
    <!-- Accessibility Skip Link -->
    <a href="#auth-card" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-zinc-900 focus:text-white focus:rounded-lg focus:shadow-lg focus:outline-none focus:ring-2 focus:ring-zinc-500 font-semibold text-sm">
        Skip to form
    </a>

    <!-- Theme Switcher -->
    <div class="fixed top-4 right-4 z-40">
        <flux:dropdown x-data align="end">
            <flux:button variant="subtle" square aria-label="Toggle theme">
                <flux:icon.sun x-show="! $flux.dark" variant="mini" class="size-5" />
                <flux:icon.moon x-show="$flux.dark" variant="mini" class="size-5" />
            </flux:button>
            <flux:menu>
                <flux:menu.item icon="sun" x-on:click="$flux.appearance = 'light'">Light</flux:menu.item>
                <flux:menu.item icon="moon" x-on:click="$flux.appearance = 'dark'">Dark</flux:menu.item>
                <flux:menu.item icon="computer-desktop" x-on:click="$flux.appearance = 'system'">System</flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </div>

    <!-- Centered Auth Container -->
    <main id="auth-card" class="w-full max-w-sm space-y-6">
        <!-- Brand & Heading -->
        <div class="text-center space-y-2">
            <flux:brand href="/" logo="{{ asset('images/logo.png') }}" name="TUTUT" class="justify-center" />
            <flux:heading size="xl">{{ $heading ?? 'Sign In' }}</flux:heading>
            <flux:subheading>{{ $subheading ?? 'Enter your credentials to continue.' }}</flux:subheading>
        </div>

        <!-- Role Navigation -->
        <flux:navbar class="justify-center border-b border-zinc-200 dark:border-zinc-800">
            <flux:navbar.item icon="academic-cap" href="{{ route('student.login') }}" wire:navigate.hover :current="request()->routeIs('student.*')">
                Candidate
            </flux:navbar.item>
            <flux:navbar.item icon="shield-check" href="{{ route('admin.login') }}" wire:navigate.hover :current="request()->routeIs('admin.*')">
                Administrator
            </flux:navbar.item>
        </flux:navbar>

        <!-- Official Flux Card -->
        <flux:card class="space-y-6">
            @if (session('error'))
                <flux:callout variant="danger" icon="exclamation-triangle">
                    {{ session('error') }}
                </flux:callout>
            @endif

            @if (session('success'))
                <flux:callout variant="success" icon="check-circle">
                    {{ session('success') }}
                </flux:callout>
            @endif

            @if ($errors->any())
                <flux:callout variant="danger" icon="exclamation-triangle">
                    {{ $errors->first() }}
                </flux:callout>
            @endif

            {{ $slot ?? '' }}
            @yield('content')
        </flux:card>

        <!-- Footer -->
        <flux:text size="xs" class="text-center">
            &copy; {{ date('Y') }} TUTUT. All rights reserved.
        </flux:text>
    </main>

    @livewireScripts
    @fluxScripts
</body>
</html>
