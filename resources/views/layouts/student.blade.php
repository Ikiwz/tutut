<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <title>@yield('title', 'Student Portal') — TUTUT</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @fluxAppearance
</head>
<body class="min-h-screen bg-zinc-50 dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 antialiased">
    <!-- Desktop Sidebar (Official Flux Layout) -->
    <flux:sidebar sticky collapsible="mobile" class="bg-white dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-800">
        <flux:sidebar.header>
            <flux:sidebar.brand
                href="{{ route('student.dashboard') }}"
                logo="{{ asset('images/logo.png') }}"
                name="TUTUT TOEFL"
            />
            <flux:sidebar.collapse class="lg:hidden" />
        </flux:sidebar.header>

        <flux:sidebar.nav>
            <flux:sidebar.item icon="home" href="{{ route('student.dashboard') }}" :current="request()->routeIs('student.dashboard')">
                My Dashboard
            </flux:sidebar.item>

            <flux:sidebar.group heading="Accessibility & Audio" class="grid">
                <flux:sidebar.item icon="speaker-wave" href="#" onclick="testAudio(); return false;" title="Test Audio (Shortcut: T)">
                    Audio Test (T)
                </flux:sidebar.item>
                <flux:sidebar.item icon="question-mark-circle" href="#" onclick="openShortcutsModal(); return false;" title="Keyboard Shortcuts (Shortcut: H)">
                    Shortcuts (H)
                </flux:sidebar.item>
            </flux:sidebar.group>
        </flux:sidebar.nav>

        <flux:sidebar.spacer />

        <!-- Controls: Dark Mode Toggle -->
        <div class="px-3 py-2 flex items-center justify-between border-t border-zinc-200 dark:border-zinc-800">
            <span class="text-xs text-zinc-500 font-medium">Appearance</span>
            <flux:button x-data x-on:click="$flux.dark = ! $flux.dark" icon="moon" variant="subtle" size="sm" aria-label="Toggle dark mode" />
        </div>

        <!-- Student Profile & Logout -->
        <flux:dropdown position="top" align="start" class="max-lg:hidden">
            <flux:sidebar.profile
                avatar="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()?->name ?? 'Student') }}&background=2563eb&color=fff"
                name="{{ auth()->user()?->name ?? 'Candidate' }}"
            />

            <flux:menu>
                <form method="POST" action="{{ route('student.logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item icon="arrow-right-start-on-rectangle" type="submit" class="w-full text-left">Log Out</flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>

    <!-- Mobile Header -->
    <flux:header class="lg:hidden bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:brand href="{{ route('student.dashboard') }}" logo="{{ asset('images/logo.png') }}" name="TUTUT" />

        <flux:spacer />

        <flux:button x-data x-on:click="$flux.dark = ! $flux.dark" icon="moon" variant="subtle" size="sm" aria-label="Toggle dark mode" />

        <flux:dropdown position="top" align="start">
            <flux:profile avatar="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()?->name ?? 'Student') }}&background=2563eb&color=fff" />

            <flux:menu>
                <form method="POST" action="{{ route('student.logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item icon="arrow-right-start-on-rectangle" type="submit" class="w-full text-left">Log Out</flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    <!-- Main Content -->
    <flux:main class="p-6 lg:p-8 max-w-7xl mx-auto w-full">
        @if (session('success'))
            <flux:callout variant="success" icon="check-circle" class="mb-6">
                {{ session('success') }}
            </flux:callout>
        @endif

        @if (session('error'))
            <flux:callout variant="danger" icon="exclamation-triangle" class="mb-6">
                {{ session('error') }}
            </flux:callout>
        @endif

        {{ $slot ?? '' }}
        @yield('content')
    </flux:main>

    <!-- Keyboard Shortcuts Modal (Flux Native) -->
    <div id="shortcutsModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm hidden items-center justify-center p-4" role="dialog" aria-modal="true" aria-label="Keyboard Shortcuts">
        <flux:card class="w-full max-w-lg space-y-4 shadow-2xl bg-white dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <flux:heading size="lg">Accessibility Keyboard Shortcuts</flux:heading>
                <flux:button size="sm" variant="ghost" icon="x-mark" onclick="closeShortcutsModal()" aria-label="Close modal" />
            </div>
            <flux:separator />

            <div class="space-y-2 text-sm">
                <div class="flex items-center justify-between py-1.5 border-b border-zinc-100 dark:border-zinc-800">
                    <span>Test Audio Device</span>
                    <kbd class="px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 font-mono text-xs font-bold">T</kbd>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-zinc-100 dark:border-zinc-800">
                    <span>Help &amp; Shortcuts Dialog</span>
                    <kbd class="px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 font-mono text-xs font-bold">H</kbd>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-zinc-100 dark:border-zinc-800">
                    <span>Select Options A, B, C, D</span>
                    <div class="flex gap-1 font-mono text-xs font-bold">
                        <kbd class="px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800">A</kbd>
                        <kbd class="px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800">B</kbd>
                        <kbd class="px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800">C</kbd>
                        <kbd class="px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800">D</kbd>
                    </div>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-zinc-100 dark:border-zinc-800">
                    <span>Next / Previous Question</span>
                    <div class="flex gap-1 font-mono text-xs font-bold">
                        <kbd class="px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800">→</kbd>
                        <kbd class="px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800">←</kbd>
                    </div>
                </div>
                <div class="flex items-center justify-between py-1.5">
                    <span>Close Active Dialog / Modal</span>
                    <kbd class="px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 font-mono text-xs font-bold">Esc</kbd>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <flux:button variant="primary" onclick="closeShortcutsModal()">Got It</flux:button>
            </div>
        </flux:card>
    </div>

    <!-- Live Speech Announcer for Visually Impaired -->
    <div id="liveAnnouncer" class="sr-only" aria-live="assertive" aria-atomic="true"></div>

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

        function openShortcutsModal() {
            const m = document.getElementById('shortcutsModal');
            if (m) {
                m.classList.remove('hidden');
                m.classList.add('flex');
                speak('Keyboard shortcuts dialog opened.');
            }
        }

        function closeShortcutsModal() {
            const m = document.getElementById('shortcutsModal');
            if (m) {
                m.classList.add('hidden');
                m.classList.remove('flex');
            }
        }

        function testAudio() {
            speak('Hello, welcome to TUTUT. Your audio output is active and working properly.');
        }

        document.addEventListener('keydown', (e) => {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;

            if (e.key === 't' || e.key === 'T') {
                e.preventDefault();
                testAudio();
            } else if (e.key === 'h' || e.key === 'H') {
                e.preventDefault();
                openShortcutsModal();
            } else if (e.key === 'Escape') {
                closeShortcutsModal();
            }
        });
    </script>

    @livewireScripts
    @fluxScripts
</body>
</html>
