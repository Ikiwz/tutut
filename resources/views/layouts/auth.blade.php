<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="notranslate" translate="no">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="google" content="notranslate">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <title>{{ $title ?? 'Student Log In' }} — TUTUT</title>

    {{-- Anti-flicker Theme Initialization before any CSS/fonts render --}}
    @fluxAppearance

    {{-- SEO & Security Metadata --}}
    <meta name="description" content="{{ $description ?? 'TUTUT Online TOEFL Examination Portal — Candidate and Administrator Authentication.' }}">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#18181b">

    {{-- Open Graph Metadata --}}
    <meta property="og:site_name" content="TUTUT">
    <meta property="og:title" content="{{ $title ?? 'Student Log In' }} — TUTUT">
    <meta property="og:description" content="TUTUT Online TOEFL Examination Portal.">
    <meta property="og:image" content="{{ asset('images/logo.png') }}">
    <meta property="og:type" content="website">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-dvh bg-zinc-50 dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 antialiased selection:bg-blue-600 selection:text-white">
    {{-- Form Column: Accessible HTML5 Main Landmark --}}
    <main class="w-full lg:w-1/2 min-h-dvh flex flex-col justify-between p-6 sm:p-8 lg:p-10 relative z-10">
        {{-- Top Bar Header: Theme Mode Switcher (Always positioned at the very top) --}}
        <header class="w-full flex justify-end items-center z-20">
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
        </header>

        <div class="w-full max-w-md mx-auto space-y-4 my-auto py-2">
            <div class="text-center">
                <flux:brand href="/" logo="{{ asset('images/logo.png') }}" name="TUTUT" class="justify-center font-bold text-lg" />
            </div>

            {{-- Main Inset Card --}}
            <flux:card body="inset" variant="soft" size="lg">
                {{-- Outer Container: Centered Greeting Header (Semantic H1) --}}
                <flux:card.header class="flex flex-col items-center text-center">
                    <flux:card.heading size="lg" level="1" class="text-center">{{ $heading ?? 'Welcome Back' }}</flux:card.heading>
                    @if (isset($subheading))
                        <flux:card.subheading class="text-center mt-1">{{ $subheading }}</flux:card.subheading>
                    @endif
                </flux:card.header>

                {{-- Inner Container: Recessed Inset Body --}}
                <flux:card.body class="space-y-6">
                    @yield('content')
                </flux:card.body>

                {{-- Outer Container: Footer --}}
                @hasSection('footer')
                    <flux:card.footer>
                        @yield('footer')
                    </flux:card.footer>
                @endif
            </flux:card>
        </div>

        {{-- Footer Links & Copyright --}}
        <footer class="w-full text-center py-2 space-y-2">
            <div class="flex items-center justify-center gap-3 text-xs text-zinc-500 dark:text-zinc-400">
                <flux:link href="{{ route('terms') }}" class="hover:underline">Terms</flux:link>
                <span>&bull;</span>
                <flux:link href="{{ route('privacy') }}" class="hover:underline">Privacy</flux:link>
                <span>&bull;</span>
                <flux:link href="{{ route('rules') }}" class="hover:underline">Exam Rules</flux:link>
                <span>&bull;</span>
                <flux:link href="{{ route('faq') }}" class="hover:underline">FAQ</flux:link>
            </div>
            <flux:text size="xs" class="text-zinc-500 dark:text-zinc-400">
                &copy; {{ date('Y') }} TUTUT. All rights reserved.
            </flux:text>
        </footer>
    </main>

    {{-- Fixed Inset Framed Image Column (Decorative Aside, Hidden from Screen Readers) --}}
    <aside aria-hidden="true" class="hidden lg:block fixed top-0 right-0 w-1/2 h-dvh p-3 z-0 pointer-events-none">
        <div class="relative w-full h-full rounded-2xl overflow-hidden border border-zinc-200 dark:border-zinc-800 shadow-xs bg-zinc-900 pointer-events-auto">
            <img
                src="https://images.unsplash.com/photo-1606326608726-07a8ee903449?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=NHwxMjA3fDA%3D"
                alt=""
                class="absolute inset-0 h-full w-full object-cover object-center"
                loading="eager"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-zinc-950/60 via-transparent to-transparent"></div>
        </div>
    </aside>

    {{-- Floating Toast Notifications --}}
    @persist('toast')
        <flux:toast position="top end" />
    @endpersist

    @livewireScripts
    @fluxScripts

    @if (session('success'))
        <script>
            (function() {
                const fire = () => {
                    if (typeof window.Flux?.toast === 'function' && document.querySelector('ui-toast')?._x_dataStack) {
                        window.Flux.toast({ text: {{ Js::from(session('success')) }}, variant: 'success', heading: 'Success', duration: 8000 });
                    } else {
                        setTimeout(fire, 50);
                    }
                };
                fire();
            })();
        </script>
    @endif
    @if (session('error'))
        <script>
            (function() {
                const fire = () => {
                    if (typeof window.Flux?.toast === 'function' && document.querySelector('ui-toast')?._x_dataStack) {
                        window.Flux.toast({ text: {{ Js::from(session('error')) }}, variant: 'danger', heading: 'Error', duration: 8000 });
                    } else {
                        setTimeout(fire, 50);
                    }
                };
                fire();
            })();
        </script>
    @endif
    @if (session('status'))
        <script>
            (function() {
                const fire = () => {
                    if (typeof window.Flux?.toast === 'function' && document.querySelector('ui-toast')?._x_dataStack) {
                        window.Flux.toast({ text: {{ Js::from(session('status')) }}, variant: 'info', heading: 'Notice', duration: 8000 });
                    } else {
                        setTimeout(fire, 50);
                    }
                };
                fire();
            })();
        </script>
    @endif
    {{-- Prevent stale session display via Back/Forward Cache (BFCache) or navigation gestures --}}
    <script>
        window.addEventListener('pageshow', function (event) {
            const perfNav = window.performance?.getEntriesByType?.('navigation')?.[0];
            if (event.persisted || perfNav?.type === 'back_forward' || window.performance?.navigation?.type === 2) {
                window.location.reload();
            }
        });
    </script>

    {{-- Client-Side Security Hardening: Disable Right-Click Context Menu & Developer Tools Shortcuts --}}
    <script>
        (function () {
            'use strict';

            // 1. Disable Right-Click Context Menu
            document.addEventListener('contextmenu', function (e) {
                e.preventDefault();
                return false;
            }, { capture: true });

            // 2. Disable DevTools & Page Inspection Shortcuts
            document.addEventListener('keydown', function (e) {
                const isMac = navigator.platform && navigator.platform.toUpperCase().indexOf('MAC') >= 0;
                const modifier = isMac ? e.metaKey : e.ctrlKey;
                const key = e.key ? e.key.toUpperCase() : '';
                const keyCode = e.keyCode || e.which;

                // F12 key
                if (key === 'F12' || keyCode === 123) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }

                // Ctrl+Shift+I / Cmd+Option+I (Inspect Element)
                // Ctrl+Shift+J / Cmd+Option+J (Developer Console)
                // Ctrl+Shift+C / Cmd+Option+C (Inspect Element Selector)
                // Ctrl+Shift+K / Cmd+Option+K (Firefox Console)
                // Ctrl+Shift+E / Cmd+Option+E (Firefox Network Tab)
                if (modifier && e.shiftKey && ['I', 'J', 'C', 'K', 'E'].includes(key)) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }

                // Ctrl+U / Cmd+U (View Page Source)
                if (modifier && (key === 'U' || keyCode === 85)) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }

                // Ctrl+S / Cmd+S (Save Page to Disk)
                if (modifier && (key === 'S' || keyCode === 83)) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }

                // Ctrl+P / Cmd+P (Print Page)
                if (modifier && (key === 'P' || keyCode === 80)) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }

                // ContextMenu key or Shift+F10 (Context Menu via Keyboard)
                if (key === 'CONTEXTMENU' || (e.shiftKey && (key === 'F10' || keyCode === 121))) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
            }, { capture: true });

            // 3. Disable Text Selection on Document (Allowing only input fields)
            document.addEventListener('selectstart', function (e) {
                if (!e.target.matches('input, textarea, [contenteditable="true"]')) {
                    e.preventDefault();
                    return false;
                }
            }, { capture: true });

            // 4. Disable Copy & Cut on Document (Allowing only input fields)
            document.addEventListener('copy', function (e) {
                if (!e.target.matches('input, textarea')) {
                    e.preventDefault();
                    return false;
                }
            }, { capture: true });

            document.addEventListener('cut', function (e) {
                if (!e.target.matches('input, textarea')) {
                    e.preventDefault();
                    return false;
                }
            }, { capture: true });

            // 5. Disable Dragging of Images and Sensitive Elements
            document.addEventListener('dragstart', function (e) {
                if (!e.target.matches('input, textarea')) {
                    e.preventDefault();
                    return false;
                }
            }, { capture: true });

            // 4. Security Console Notice
            try {
                if (window.console && typeof window.console.log === 'function') {
                    console.clear();
                    console.log(
                        '%cSecurity Warning%c: Developer mode, element inspection, and script modification are disabled on this portal for academic examination integrity.',
                        'color: #ef4444; font-size: 16px; font-weight: bold;',
                        'color: inherit; font-size: 13px;'
                    );
                }
            } catch (_) {}
        })();
    </script>
</body>
</html>
