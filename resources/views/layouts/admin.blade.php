<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <title>{{ $title ?? 'Admin Dashboard' }} — TUTUT</title>
    <meta name="description" content="TUTUT Examination Management and Proctoring Portal.">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#18181b">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @fluxAppearance
</head>
<body class="min-h-dvh bg-zinc-50 dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 antialiased selection:bg-blue-600 selection:text-white transition-colors duration-200">
    <!-- Skip to Content Link for Accessibility -->
    <a href="#admin-main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-blue-600 focus:text-white focus:rounded-lg focus:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 font-bold text-sm">
        Skip to content
    </a>

    <!-- Desktop Sidebar (Official Flux Layout) -->
    <flux:sidebar sticky collapsible class="bg-zinc-50 dark:bg-zinc-900">
        <flux:sidebar.header>
            <flux:sidebar.brand
                href="{{ route('admin.dashboard') }}"
                logo="{{ asset('images/logo.png') }}"
                name="TUTUT Admin"
            />
            <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
        </flux:sidebar.header>

        <flux:sidebar.nav role="navigation" aria-label="Main Administration Menu">
            <flux:sidebar.item icon="home" href="{{ route('admin.dashboard') }}" wire:navigate.hover :current="request()->routeIs('admin.dashboard')">
                Dashboard Overview
            </flux:sidebar.item>

            <flux:sidebar.group heading="Test Management" class="grid">
                <flux:sidebar.item icon="clipboard-document-list" href="{{ route('admin.test-sessions.index') }}" wire:navigate.hover :current="request()->routeIs('admin.test-sessions.*')">
                    Test Sessions
                </flux:sidebar.item>
                <flux:sidebar.item icon="academic-cap" href="{{ route('admin.sections.index') }}" wire:navigate.hover :current="request()->routeIs('admin.sections.*')">
                    Exam Sections
                </flux:sidebar.item>
                <flux:sidebar.item icon="question-mark-circle" href="{{ route('admin.questions.index') }}" wire:navigate.hover :current="request()->routeIs('admin.questions.*')">
                    Questions Vault
                </flux:sidebar.item>
                <flux:sidebar.item icon="document-text" href="{{ route('admin.passages.index') }}" wire:navigate.hover :current="request()->routeIs('admin.passages.*')">
                    Reading Passages
                </flux:sidebar.item>
                <flux:sidebar.item icon="bookmark" href="{{ route('admin.directions.index') }}" wire:navigate.hover :current="request()->routeIs('admin.directions.*')">
                    Directions &amp; Audio
                </flux:sidebar.item>
            </flux:sidebar.group>

            <flux:sidebar.group heading="Reports &amp; Students" class="grid">
                <flux:sidebar.item icon="chart-bar" href="{{ route('admin.attempts.index') }}" wire:navigate.hover :current="request()->routeIs('admin.attempts.*')">
                    Exam Results &amp; Audit
                </flux:sidebar.item>
                <flux:sidebar.item icon="users" href="{{ route('admin.students.index') }}" wire:navigate.hover :current="request()->routeIs('admin.students.*')">
                    Student Verification
                </flux:sidebar.item>
            </flux:sidebar.group>
        </flux:sidebar.nav>

        <flux:sidebar.spacer />

        <!-- Appearance / Dark Mode Switcher -->
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

        <!-- Administrator Profile & Logout -->
        <flux:dropdown position="top" align="start" class="max-lg:hidden">
            <flux:sidebar.profile
                avatar="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()?->name ?? 'Admin') }}&background=2563eb&color=fff"
                name="{{ auth()->user()?->name ?? 'Administrator' }}"
            />

            <flux:menu>
                <form method="POST" action="{{ route('admin.logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item icon="arrow-right-start-on-rectangle" type="submit" class="w-full text-left">
                        Sign Out
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>

    <!-- Mobile Header -->
    <flux:header class="lg:hidden bg-white dark:bg-zinc-800">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:brand href="{{ route('admin.dashboard') }}" logo="{{ asset('images/logo.png') }}" name="TUTUT Admin" class="max-sm:hidden" />

        <flux:spacer />

        <!-- Theme Toggle on Mobile -->
        <flux:button x-data x-on:click="$flux.dark = ! $flux.dark" icon="moon" variant="subtle" size="sm" aria-label="Toggle dark mode" />

        <flux:dropdown position="top" align="start">
            <flux:profile avatar="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()?->name ?? 'Admin') }}&background=2563eb&color=fff" />

            <flux:menu>
                <form method="POST" action="{{ route('admin.logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item icon="arrow-right-start-on-rectangle" type="submit" class="w-full text-left">
                        Sign Out
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    <!-- Main Content Region (Official Flux Inset Layout) -->
    <flux:main container inset class="lg:ms-0" id="admin-main-content">
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

    {{-- Floating Toast Notifications --}}
    @persist('toast')
        <flux:toast position="top end" />
    @endpersist

    @if (session('success'))
        <div x-data x-init="$nextTick(() => $flux.toast({ text: {{ Js::from(session('success')) }}, variant: 'success', heading: 'Success' }))"></div>
    @endif
    @if (session('error'))
        <div x-data x-init="$nextTick(() => $flux.toast({ text: {{ Js::from(session('error')) }}, variant: 'danger', heading: 'Error' }))"></div>
    @endif

    @livewireScripts
    @fluxScripts
</body>
</html>
