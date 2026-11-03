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
    <!-- Skip to Main Content Link for Screen Readers (Accessibility WCAG 2.1 AA) -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-blue-600 focus:text-white focus:rounded-lg focus:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 font-bold text-sm">
        Skip to authentication form
    </a>

    <!-- Top Navigation Controls -->
    <header role="banner" class="fixed top-4 right-4 flex items-center gap-2 z-40">
        <!-- Official Flux Dark/Light Mode Toggle -->
        <flux:dropdown x-data align="end">
            <flux:button variant="subtle" square class="group" aria-label="Toggle appearance / color scheme">
                <flux:icon.sun x-show="! $flux.dark" variant="mini" class="text-zinc-700 dark:text-zinc-200 size-5" />
                <flux:icon.moon x-show="$flux.dark" variant="mini" class="text-zinc-700 dark:text-zinc-200 size-5" />
            </flux:button>
            <flux:menu>
                <flux:menu.item icon="sun" x-on:click="$flux.appearance = 'light'">Light Mode</flux:menu.item>
                <flux:menu.item icon="moon" x-on:click="$flux.appearance = 'dark'">Dark Mode</flux:menu.item>
                <flux:menu.item icon="computer-desktop" x-on:click="$flux.appearance = 'system'">System Preference</flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </header>

    <!-- Main Authentication Container -->
    <main id="main-content" role="main" class="w-full max-w-md space-y-6 my-auto">
        <!-- Platform Header -->
        <header class="text-center space-y-2">
            <a href="/" class="inline-block transition-transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-xl" aria-label="TUTUT Homepage">
                <img src="{{ asset('images/logo.png') }}" alt="TUTUT Official Logo" class="h-16 w-auto mx-auto" width="64" height="64">
            </a>
            <flux:heading size="xl" level="1" class="font-extrabold tracking-tight text-2xl sm:text-3xl text-zinc-900 dark:text-white">
                {{ $heading ?? 'TUTUT Examination' }}
            </flux:heading>
            <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">
                {{ $subheading ?? 'Accessible Campus TOEFL Testing System' }}
            </flux:text>
        </header>

        <!-- Role Navigation Tabs (Accessible Tablist) -->
        <nav role="tablist" aria-label="Select portal role" class="grid grid-cols-2 p-1.5 rounded-xl bg-zinc-200/80 dark:bg-zinc-800/80 text-xs font-semibold gap-1">
            <a href="{{ route('student.login') }}" role="tab" aria-selected="{{ request()->routeIs('student.login') || request()->routeIs('student.register') ? 'true' : 'false' }}" class="text-center py-2 px-3 rounded-lg transition-all {{ request()->routeIs('student.login') || request()->routeIs('student.register') ? 'bg-white dark:bg-zinc-900 text-blue-600 dark:text-blue-400 shadow-sm font-bold' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white' }}">
                Candidate (Student)
            </a>
            <a href="{{ route('admin.login') }}" role="tab" aria-selected="{{ request()->routeIs('admin.login') ? 'true' : 'false' }}" class="text-center py-2 px-3 rounded-lg transition-all {{ request()->routeIs('admin.login') ? 'bg-white dark:bg-zinc-900 text-blue-600 dark:text-blue-400 shadow-sm font-bold' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white' }}">
                Administrator
            </a>
        </nav>

        <!-- Main Card Section -->
        <section aria-labelledby="form-heading" class="w-full">
            <flux:card class="p-6 sm:p-8 space-y-6 shadow-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 rounded-2xl">
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
        </section>

        <!-- Semantic Footer -->
        <footer role="contentinfo" class="text-center text-xs text-zinc-500 dark:text-zinc-400 space-y-1">
            <p>&copy; {{ date('Y') }} TUTUT — TOEFL Examination System for the Blind.</p>
            <p class="text-[11px] text-zinc-400 dark:text-zinc-500">Accessible Multi-Modal Testing • WCAG 2.1 Compliant</p>
        </footer>
    </main>

    @fluxScripts
</body>
</html>
