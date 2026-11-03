<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <title>@yield('title', 'Student Portal') — TUTUT</title>
    <meta name="description" content="TUTUT Online TOEFL Examination Portal.">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @fluxAppearance
</head>
<body class="min-h-dvh bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 antialiased selection:bg-blue-600 selection:text-white transition-colors duration-200">
    <!-- Skip to Content Link for Accessibility -->
    <a href="#student-main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-blue-600 focus:text-white focus:rounded-lg focus:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 font-bold text-sm">
        Skip to content
    </a>

    <!-- Desktop Sidebar (Official Flux Layout) -->
    <flux:sidebar sticky collapsible="mobile" class="bg-zinc-50 dark:bg-zinc-950 border-r border-zinc-200 dark:border-zinc-800">
        <flux:sidebar.header>
            <flux:sidebar.brand
                href="{{ route('student.dashboard') }}"
                logo="{{ asset('images/logo.png') }}"
                name="TUTUT TOEFL"
            />
            <flux:sidebar.collapse class="lg:hidden" />
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
    <flux:header class="lg:hidden bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800">
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
    <flux:main container inset id="student-main-content">
        @if (session('success'))
            <flux:callout variant="success" icon="check-circle" role="status" class="mb-6">
                {{ session('success') }}
            </flux:callout>
        @endif

        @if (session('error'))
            <flux:callout variant="danger" icon="exclamation-triangle" role="alert" class="mb-6">
                {{ session('error') }}
            </flux:callout>
        @endif

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

    @livewireScripts
    @fluxScripts
</body>
</html>
