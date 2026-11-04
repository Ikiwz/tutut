<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <title>{{ $title ?? 'Guidelines & Policies' }} — TUTUT</title>

    {{-- Anti-flicker Theme Initialization before CSS/fonts render --}}
    @fluxAppearance

    {{-- SEO & Metadata --}}
    <meta name="description" content="TUTUT Institutional English Proficiency Test (EPT) Policies, Terms of Service, and Exam Rules.">
    <meta name="theme-color" content="#18181b">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-dvh bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 antialiased selection:bg-blue-600 selection:text-white flex flex-col justify-between">
    {{-- Header Navigation Bar --}}
    <header class="sticky top-0 z-30 w-full bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md border-b border-zinc-200 dark:border-zinc-800">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
            {{-- Brand Logo --}}
            <div class="flex items-center gap-6">
                <flux:brand href="/" logo="{{ asset('images/logo.png') }}" name="TUTUT" class="font-bold text-lg" />
            </div>

            {{-- Policy Navigation Tabs (Desktop) --}}
            <nav class="hidden md:flex items-center gap-1 text-sm font-medium">
                <flux:button
                    variant="{{ request()->routeIs('terms') ? 'primary' : 'ghost' }}"
                    size="sm"
                    href="{{ route('terms') }}"
                    wire:navigate.hover
                >
                    Terms of Service
                </flux:button>
                <flux:button
                    variant="{{ request()->routeIs('privacy') ? 'primary' : 'ghost' }}"
                    size="sm"
                    href="{{ route('privacy') }}"
                    wire:navigate.hover
                >
                    Privacy Policy
                </flux:button>
                <flux:button
                    variant="{{ request()->routeIs('rules') ? 'primary' : 'ghost' }}"
                    size="sm"
                    href="{{ route('rules') }}"
                    wire:navigate.hover
                >
                    Exam Rules
                </flux:button>
                <flux:button
                    variant="{{ request()->routeIs('faq') ? 'primary' : 'ghost' }}"
                    size="sm"
                    href="{{ route('faq') }}"
                    wire:navigate.hover
                >
                    FAQ
                </flux:button>
            </nav>

            {{-- Action Controls & Theme Toggle --}}
            <div class="flex items-center gap-2">
                <flux:dropdown x-data align="end">
                    <flux:button variant="subtle" size="sm" square aria-label="Toggle color scheme">
                        <flux:icon.sun x-show="! $flux.dark" variant="mini" class="size-4 text-zinc-700 dark:text-zinc-200" />
                        <flux:icon.moon x-show="$flux.dark" variant="mini" class="size-4 text-zinc-700 dark:text-zinc-200" />
                    </flux:button>
                    <flux:menu>
                        <flux:menu.item icon="sun" x-on:click="$flux.appearance = 'light'">Light Mode</flux:menu.item>
                        <flux:menu.item icon="moon" x-on:click="$flux.appearance = 'dark'">Dark Mode</flux:menu.item>
                        <flux:menu.item icon="computer-desktop" x-on:click="$flux.appearance = 'system'">System Preference</flux:menu.item>
                    </flux:menu>
                </flux:dropdown>

                @auth
                    <flux:button variant="subtle" size="sm" href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('student.dashboard') }}">
                        Dashboard
                    </flux:button>
                @else
                    <flux:button variant="subtle" size="sm" href="{{ route('student.login') }}">
                        Log In
                    </flux:button>
                @endauth
            </div>
        </div>

        {{-- Mobile Navigation Strip --}}
        <div class="md:hidden flex items-center justify-around border-t border-zinc-200 dark:border-zinc-800 py-2 px-2 bg-zinc-50 dark:bg-zinc-900/50 text-xs overflow-x-auto">
            <flux:link href="{{ route('terms') }}" class="px-2 py-1 {{ request()->routeIs('terms') ? 'font-bold text-blue-600 dark:text-blue-400' : '' }}">Terms</flux:link>
            <flux:link href="{{ route('privacy') }}" class="px-2 py-1 {{ request()->routeIs('privacy') ? 'font-bold text-blue-600 dark:text-blue-400' : '' }}">Privacy</flux:link>
            <flux:link href="{{ route('rules') }}" class="px-2 py-1 {{ request()->routeIs('rules') ? 'font-bold text-blue-600 dark:text-blue-400' : '' }}">Exam Rules</flux:link>
            <flux:link href="{{ route('faq') }}" class="px-2 py-1 {{ request()->routeIs('faq') ? 'font-bold text-blue-600 dark:text-blue-400' : '' }}">FAQ</flux:link>
        </div>
    </header>

    {{-- Main Document Content --}}
    <main class="w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 flex-1">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="w-full border-t border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 py-6 text-center">
        <div class="max-w-5xl mx-auto px-4 space-y-2">
            <div class="flex items-center justify-center gap-4 text-xs text-zinc-500 dark:text-zinc-400">
                <flux:link href="{{ route('terms') }}">Terms of Service</flux:link>
                <span>&bull;</span>
                <flux:link href="{{ route('privacy') }}">Privacy Policy</flux:link>
                <span>&bull;</span>
                <flux:link href="{{ route('rules') }}">Exam Rules</flux:link>
                <span>&bull;</span>
                <flux:link href="{{ route('faq') }}">FAQ</flux:link>
            </div>
            <flux:text size="xs" class="text-zinc-500 dark:text-zinc-400">
                &copy; {{ date('Y') }} TUTUT. All rights reserved.
            </flux:text>
            <p class="text-[11px] text-zinc-400 dark:text-zinc-500 max-w-xl mx-auto leading-relaxed pt-1">
                TOEFL® is a registered trademark of Educational Testing Service (ETS). This portal administers an independent institutional English Proficiency Test (EPT) for university academic evaluation and is not affiliated with or endorsed by ETS.
            </p>
        </div>
    </footer>

    @livewireScripts
    @fluxScripts

    {{-- Client-Side Security Hardening: Disable Right-Click Context Menu & Developer Tools Shortcuts --}}
    <script>
        (function () {
            'use strict';

            document.addEventListener('contextmenu', function (e) {
                e.preventDefault();
                return false;
            }, { capture: true });

            document.addEventListener('keydown', function (e) {
                const isMac = navigator.platform && navigator.platform.toUpperCase().indexOf('MAC') >= 0;
                const modifier = isMac ? e.metaKey : e.ctrlKey;
                const key = e.key ? e.key.toUpperCase() : '';
                const keyCode = e.keyCode || e.which;

                if (key === 'F12' || keyCode === 123) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }

                if (modifier && e.shiftKey && ['I', 'J', 'C', 'K', 'E'].includes(key)) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }

                if (modifier && (key === 'U' || keyCode === 85)) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }

                if (modifier && (key === 'S' || keyCode === 83)) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }

                if (key === 'CONTEXTMENU' || (e.shiftKey && (key === 'F10' || keyCode === 121))) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
            }, { capture: true });
        })();
    </script>
</body>
</html>

