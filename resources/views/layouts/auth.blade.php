<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <title>{{ $title ?? 'Login' }} — TUTUT (TOEFL Examination)</title>
    <meta name="description" content="TUTUT - Official Accessible TOEFL Examination Platform for Visually Impaired and Blind Candidates.">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>
<body class="min-h-screen bg-zinc-100 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 flex flex-col items-center justify-center p-4 sm:p-6 antialiased selection:bg-blue-600 selection:text-white transition-colors duration-200">
    <!-- Skip to Main Content Link for Screen Readers (WCAG 2.1 AA) -->
    <a href="#auth-card" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-blue-600 focus:text-white focus:rounded-lg focus:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 font-bold text-sm">
        Skip to authentication form
    </a>

    <!-- Top Navigation Controls -->
    <div class="fixed top-4 right-4 z-40">
        <!-- Official Flux Dark/Light Mode Switcher -->
        <flux:dropdown x-data align="end">
            <flux:button variant="subtle" square aria-label="Toggle color scheme">
                <flux:icon.sun x-show="! $flux.dark" variant="mini" class="size-5 text-zinc-700 dark:text-zinc-200" />
                <flux:icon.moon x-show="$flux.dark" variant="mini" class="size-5 text-zinc-700 dark:text-zinc-200" />
            </flux:button>
            <flux:menu>
                <flux:menu.item icon="sun" x-on:click="$flux.appearance = 'light'">Light Mode</flux:menu.item>
                <flux:menu.item icon="moon" x-on:click="$flux.appearance = 'dark'">Dark Mode</flux:menu.item>
                <flux:menu.item icon="computer-desktop" x-on:click="$flux.appearance = 'system'">System Preference</flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </div>

    <!-- Main Authentication Container (Official Flux Main & Card) -->
    <flux:main id="auth-card" class="w-full max-w-md my-auto space-y-6 !p-0">
        <!-- Platform Header & Hierarchy -->
        <div class="text-center space-y-2">
            <flux:brand href="/" logo="{{ asset('images/logo.png') }}" name="TUTUT" class="mx-auto" />
            <flux:heading level="1" size="xl" class="font-extrabold tracking-tight text-2xl sm:text-3xl">
                {{ $heading ?? 'TUTUT Examination' }}
            </flux:heading>
            <flux:subheading size="sm">
                {{ $subheading ?? 'Accessible Campus TOEFL Testing System' }}
            </flux:subheading>
        </div>

        <!-- Official Flux Navbar for Role Switching -->
        <flux:navbar class="w-full justify-center border-b border-zinc-200 dark:border-zinc-800 pb-2">
            <flux:navbar.item icon="academic-cap" href="{{ route('student.login') }}" :current="request()->routeIs('student.login') || request()->routeIs('student.register')">
                Candidate
            </flux:navbar.item>
            <flux:navbar.item icon="shield-check" href="{{ route('admin.login') }}" :current="request()->routeIs('admin.login')">
                Administrator
            </flux:navbar.item>
        </flux:navbar>

        <!-- Main Card Container -->
        <flux:card class="p-6 sm:p-8 space-y-5 shadow-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 rounded-2xl">
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

        <!-- Semantic Footer via Flux -->
        <flux:footer class="text-center !p-0 space-y-1">
            <flux:text size="xs" class="text-zinc-500 dark:text-zinc-400">
                &copy; {{ date('Y') }} TUTUT — TOEFL Examination System for the Blind.
            </flux:text>
            <flux:text size="xs" class="text-zinc-400 dark:text-zinc-500">
                Accessible Multi-Modal Testing • WCAG 2.1 AA Compliant
            </flux:text>
        </flux:footer>
    </flux:main>

    @fluxScripts
</body>
</html>
