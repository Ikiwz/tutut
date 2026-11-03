<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <title>{{ $title ?? 'Sign In' }} — TUTUT</title>
    <meta name="description" content="TUTUT Online TOEFL Examination Platform.">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @fluxAppearance
</head>
<body class="min-h-screen bg-zinc-100/80 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 flex flex-col items-center justify-center p-4 sm:p-6 antialiased selection:bg-blue-600 selection:text-white transition-colors duration-200">
    <!-- Skip to Content Link for Accessibility -->
    <a href="#auth-card" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-blue-600 focus:text-white focus:rounded-lg focus:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 font-semibold text-sm transition-all duration-200">
        Skip to form
    </a>

    <!-- Top Right Theme Mode Switcher -->
    <div class="fixed top-4 right-4 z-40">
        <flux:dropdown x-data align="end">
            <flux:button variant="subtle" square aria-label="Toggle theme">
                <flux:icon.sun x-show="! $flux.dark" variant="mini" class="size-5 text-zinc-700 dark:text-zinc-200" />
                <flux:icon.moon x-show="$flux.dark" variant="mini" class="size-5 text-zinc-700 dark:text-zinc-200" />
            </flux:button>
            <flux:menu>
                <flux:menu.item icon="sun" x-on:click="$flux.appearance = 'light'">Light</flux:menu.item>
                <flux:menu.item icon="moon" x-on:click="$flux.appearance = 'dark'">Dark</flux:menu.item>
                <flux:menu.item icon="computer-desktop" x-on:click="$flux.appearance = 'system'">System</flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </div>

    <!-- Centered Form Container -->
    <main id="auth-card" role="main" class="w-full max-w-md my-auto space-y-6">
        <!-- Logo & Header (Centered & Balanced) -->
        <div class="flex flex-col items-center justify-center text-center space-y-2">
            <a href="/" wire:navigate.hover class="inline-flex items-center justify-center p-2 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-xs hover:scale-105 transition-transform duration-200" aria-label="TUTUT Homepage">
                <img src="{{ asset('images/logo.png') }}" alt="TUTUT Logo" class="h-10 w-auto" width="40" height="40" />
            </a>
            <flux:heading level="1" size="xl" class="font-bold tracking-tight text-2xl sm:text-3xl text-zinc-900 dark:text-white">
                {{ $heading ?? 'Sign In' }}
            </flux:heading>
            <flux:subheading size="sm" class="text-zinc-500 dark:text-zinc-400">
                {{ $subheading ?? 'Enter your credentials to continue.' }}
            </flux:subheading>
        </div>

        <!-- Proportional Segmented Role Switcher (SPA Wire-Navigate) -->
        <nav role="tablist" aria-label="Select role" class="grid grid-cols-2 p-1 rounded-xl bg-zinc-200/70 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700/60 text-xs font-semibold gap-1">
            <a href="{{ route('student.login') }}" wire:navigate.hover role="tab" aria-selected="{{ request()->routeIs('student.login') || request()->routeIs('student.register') ? 'true' : 'false' }}" class="flex items-center justify-center gap-2 py-2 px-3 rounded-lg transition-all duration-200 {{ request()->routeIs('student.login') || request()->routeIs('student.register') ? 'bg-white dark:bg-zinc-900 text-blue-600 dark:text-blue-400 shadow-sm font-bold' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white' }}">
                <flux:icon icon="academic-cap" class="size-4" />
                <span>Candidate</span>
            </a>
            <a href="{{ route('admin.login') }}" wire:navigate.hover role="tab" aria-selected="{{ request()->routeIs('admin.login') ? 'true' : 'false' }}" class="flex items-center justify-center gap-2 py-2 px-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.login') ? 'bg-white dark:bg-zinc-900 text-blue-600 dark:text-blue-400 shadow-sm font-bold' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white' }}">
                <flux:icon icon="shield-check" class="size-4" />
                <span>Administrator</span>
            </a>
        </nav>

        <!-- Form Card with Subtle Bezel Frame -->
        <flux:card class="p-6 sm:p-8 space-y-5 shadow-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 rounded-2xl transition-all duration-200">
            @if (session('error'))
                <flux:callout variant="danger" icon="exclamation-triangle" role="alert">
                    {{ session('error') }}
                </flux:callout>
            @endif

            @if (session('success'))
                <flux:callout variant="success" icon="check-circle" role="status">
                    {{ session('success') }}
                </flux:callout>
            @endif

            @if ($errors->any())
                <flux:callout variant="danger" icon="exclamation-triangle" role="alert">
                    {{ $errors->first() }}
                </flux:callout>
            @endif

            {{ $slot ?? '' }}
            @yield('content')
        </flux:card>

        <!-- Minimal Footer -->
        <footer role="contentinfo" class="text-center">
            <flux:text size="xs" class="text-zinc-500 dark:text-zinc-400">
                &copy; {{ date('Y') }} TUTUT. All rights reserved.
            </flux:text>
        </footer>
    </main>

    @livewireScripts
    @fluxScripts
</body>
</html>
