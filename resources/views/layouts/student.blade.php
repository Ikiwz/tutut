<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth notranslate" translate="no">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="google" content="notranslate">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <title>@yield('title', 'Student Portal') — TUTUT</title>
    <meta name="description" content="TUTUT Online TOEFL Examination Portal.">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#18181b">

    {{-- Anti-flicker Theme Initialization before any CSS/fonts render --}}
    @fluxAppearance

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-dvh bg-zinc-50 dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 antialiased selection:bg-blue-600 selection:text-white">
    <!-- Skip to Content Link for Accessibility -->
    <a href="#student-main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-blue-600 focus:text-white focus:rounded-lg focus:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 font-bold text-sm">
        Skip to content
    </a>

    <!-- Desktop Sidebar (Official Flux Layout) -->
    <flux:sidebar sticky collapsible class="bg-zinc-50 dark:bg-zinc-900">
        <flux:sidebar.header>
            <flux:sidebar.brand
                href="{{ route('student.dashboard') }}"
                logo="{{ asset('images/logo.png') }}"
                name="TUTUT TOEFL"
            />
            <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
        </flux:sidebar.header>

        <flux:sidebar.nav role="navigation" aria-label="Candidate Navigation">
            <flux:sidebar.item icon="home" href="{{ route('student.dashboard') }}" wire:navigate.hover :current="request()->routeIs('student.dashboard')">
                My Dashboard
            </flux:sidebar.item>

            <flux:sidebar.group heading="Accessibility &amp; Tools" class="grid">
                <flux:sidebar.item icon="speaker-wave" href="#" onclick="testAudio(); return false;" title="Test Audio Device (Shortcut: T)">
                    Audio Test (T)
                </flux:sidebar.item>
                <flux:sidebar.item icon="question-mark-circle" x-data x-on:click="$flux.modal('shortcuts-modal').show()" title="Keyboard Accessibility Shortcuts (Shortcut: H)">
                    Shortcuts Dialog (H)
                </flux:sidebar.item>
            </flux:sidebar.group>
        </flux:sidebar.nav>

        <flux:sidebar.spacer />

        <!-- Theme Mode Switcher -->
        <div class="px-3 py-2 flex items-center justify-between border-t border-zinc-200 dark:border-zinc-800 text-xs text-zinc-500">
            <span class="font-medium">Theme Mode</span>
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
        </div>

        <!-- Candidate Profile & Logout -->
        <flux:dropdown position="top" align="start" class="max-lg:hidden">
            <flux:sidebar.profile
                avatar="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()?->name ?? 'Candidate') }}&background=2563eb&color=fff"
                name="{{ auth()->user()?->name ?? 'Candidate' }}"
            />

            <flux:menu>
                <form method="POST" action="{{ route('student.logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item icon="arrow-right-start-on-rectangle" type="submit" class="w-full text-left">
                        Log Out
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>

    <!-- Mobile Header -->
    <flux:header class="lg:hidden bg-white dark:bg-zinc-800">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:brand href="{{ route('student.dashboard') }}" logo="{{ asset('images/logo.png') }}" name="TUTUT" />

        <flux:spacer />

        <!-- Theme Toggle on Mobile -->
        <flux:button x-data x-on:click="$flux.dark = ! $flux.dark" icon="moon" variant="subtle" size="sm" aria-label="Toggle dark mode" />

        <flux:dropdown position="top" align="start">
            <flux:profile avatar="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()?->name ?? 'Candidate') }}&background=2563eb&color=fff" />

            <flux:menu>
                <form method="POST" action="{{ route('student.logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item icon="arrow-right-start-on-rectangle" type="submit" class="w-full text-left">
                        Log Out
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    <!-- Main Content (Official Flux Inset Layout) -->
    <flux:main container inset class="lg:ms-0" id="student-main-content">
        {{ $slot ?? '' }}
        @yield('content')
    </flux:main>

    <!-- Keyboard Shortcuts Modal (Official Flux Modal) -->
    <flux:modal name="shortcuts-modal" class="md:w-lg space-y-4">
        <div>
            <flux:heading level="2" size="lg">Accessibility Keyboard Shortcuts</flux:heading>
            <flux:subheading>Keyboard shortcuts for examination sessions</flux:subheading>
        </div>

        <flux:separator />

        <div class="space-y-3 text-sm" role="list">
            <div role="listitem" class="flex items-center justify-between py-1.5 border-b border-zinc-100 dark:border-zinc-800">
                <flux:text>Test Audio Output (TTS Voice)</flux:text>
                <kbd class="px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 font-mono text-xs font-bold border border-zinc-200 dark:border-zinc-700">T</kbd>
            </div>
            <div role="listitem" class="flex items-center justify-between py-1.5 border-b border-zinc-100 dark:border-zinc-800">
                <flux:text>Open Keyboard Shortcuts Help</flux:text>
                <kbd class="px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 font-mono text-xs font-bold border border-zinc-200 dark:border-zinc-700">H</kbd>
            </div>
            <div role="listitem" class="flex items-center justify-between py-1.5 border-b border-zinc-100 dark:border-zinc-800">
                <flux:text>Choose Options A, B, C, D</flux:text>
                <div class="flex gap-1 font-mono text-xs font-bold">
                    <kbd class="px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700">A</kbd>
                    <kbd class="px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700">B</kbd>
                    <kbd class="px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700">C</kbd>
                    <kbd class="px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700">D</kbd>
                </div>
            </div>
            <div role="listitem" class="flex items-center justify-between py-1.5 border-b border-zinc-100 dark:border-zinc-800">
                <flux:text>Navigate Next / Previous Question</flux:text>
                <div class="flex gap-1 font-mono text-xs font-bold">
                    <kbd class="px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700">→</kbd>
                    <kbd class="px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700">←</kbd>
                </div>
            </div>
            <div role="listitem" class="flex items-center justify-between py-1.5">
                <flux:text>Close Active Dialog</flux:text>
                <kbd class="px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 font-mono text-xs font-bold border border-zinc-200 dark:border-zinc-700">Esc</kbd>
            </div>
        </div>

        <div class="flex justify-end pt-2">
            <flux:modal.close>
                <flux:button variant="primary">Dismiss</flux:button>
            </flux:modal.close>
        </div>
    </flux:modal>

    <!-- Live Announcer for Screen Readers -->
    <div id="liveAnnouncer" class="sr-only" aria-live="polite" aria-atomic="true"></div>

    <script>
        function announce(text) {
            const el = document.getElementById('liveAnnouncer');
            if (el) el.textContent = text;
        }

        function speak(text) {
            announce(text);
            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel();
                const utterance = new SpeechSynthesisUtterance(text);
                utterance.lang = 'en-US';
                utterance.rate = 1.0;
                window.speechSynthesis.speak(utterance);
            }
        }

        function testAudio() {
            speak('Audio output is active and functioning properly.');
        }

        document.addEventListener('keydown', (e) => {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;

            if (e.key === 't' || e.key === 'T') {
                e.preventDefault();
                testAudio();
            } else if (e.key === 'h' || e.key === 'H') {
                e.preventDefault();
                if (window.Flux && window.Flux.modal) {
                    window.Flux.modal('shortcuts-modal').show();
                    speak('Keyboard shortcuts opened.');
                }
            }
        });
    </script>

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

        if (window.history && window.history.pushState) {
            window.history.pushState(null, '', window.location.href);
            window.addEventListener('popstate', function () {
                window.history.pushState(null, '', window.location.href);
            });
        }
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

            // 6. Anti-Tab Switching Monitor During Examination Take
            @if (request()->routeIs('exam.take'))
                let blurWarningTriggered = false;
                window.addEventListener('visibilitychange', function () {
                    if (document.hidden) {
                        if (typeof window.Flux?.toast === 'function' && !blurWarningTriggered) {
                            blurWarningTriggered = true;
                            window.Flux.toast({
                                heading: 'Integrity Warning',
                                text: 'Tab switching and leaving the examination window are monitored and recorded.',
                                variant: 'warning',
                                duration: 7000
                            });
                            setTimeout(() => { blurWarningTriggered = false; }, 10000);
                        }
                    }
                });
            @endif

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
