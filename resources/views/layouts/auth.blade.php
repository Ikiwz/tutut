<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <title>{{ $title ?? 'Login' }} — TUTUT</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>
<body class="min-h-screen bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 flex flex-col items-center justify-center p-4 sm:p-6 antialiased selection:bg-blue-500 selection:text-white">
    <!-- Top Bar Controls -->
    <div class="fixed top-4 right-4 flex items-center gap-2 z-50">
        <flux:button x-data x-on:click="$flux.dark = ! $flux.dark" icon="moon" variant="subtle" aria-label="Toggle dark mode" />
    </div>

    <!-- Auth Container -->
    <div class="w-full max-w-md space-y-6 my-auto">
        <!-- Logo & Header -->
        <div class="text-center space-y-2">
            <a href="/" class="inline-block transition-transform hover:scale-105">
                <img src="{{ asset('images/logo.png') }}" alt="TUTUT Logo" class="h-16 w-auto mx-auto" aria-hidden="true">
            </a>
            <flux:heading size="xl" level="1" class="font-extrabold tracking-tight">
                {{ $heading ?? 'TUTUT Examination' }}
            </flux:heading>
            <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">
                {{ $subheading ?? 'Accessible TOEFL Testing Platform' }}
            </flux:text>
        </div>

        <!-- Role Navigation Tabs (Uniform for All Roles) -->
        <div class="flex items-center justify-center p-1 rounded-xl bg-zinc-200/70 dark:bg-zinc-800/80 text-xs font-semibold">
            <a href="{{ route('student.login') }}" class="flex-1 text-center py-2 px-3 rounded-lg transition-all {{ request()->routeIs('student.login') || request()->routeIs('student.register') ? 'bg-white dark:bg-zinc-900 text-blue-600 dark:text-blue-400 shadow-sm font-bold' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white' }}">
                Candidate (Student)
            </a>
            <a href="{{ route('admin.login') }}" class="flex-1 text-center py-2 px-3 rounded-lg transition-all {{ request()->routeIs('admin.login') ? 'bg-white dark:bg-zinc-900 text-blue-600 dark:text-blue-400 shadow-sm font-bold' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white' }}">
                Administrator
            </a>
        </div>

        <!-- Card Content -->
        <flux:card class="p-6 sm:p-8 space-y-6 shadow-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 rounded-2xl">
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
        <div class="text-center text-xs text-zinc-500 dark:text-zinc-400 space-y-1">
            <p>&copy; {{ date('Y') }} TUTUT — TOEFL Platform for the Blind & Visually Impaired.</p>
        </div>
    </div>

    @fluxScripts
</body>
</html>
