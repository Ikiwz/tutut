<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <title>{{ $title ?? 'Log In' }} — TUTUT</title>

    {{-- SEO & Security Metadata --}}
    <meta name="description" content="{{ $description ?? 'TUTUT Online TOEFL Examination Portal — Candidate and Administrator Authentication.' }}">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#18181b">

    {{-- Open Graph Metadata --}}
    <meta property="og:site_name" content="TUTUT">
    <meta property="og:title" content="{{ $title ?? 'Log In' }} — TUTUT">
    <meta property="og:description" content="TUTUT Online TOEFL Examination Portal.">
    <meta property="og:image" content="{{ asset('images/logo.png') }}">
    <meta property="og:type" content="website">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @fluxAppearance
</head>
<body class="min-h-dvh bg-zinc-50 dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 antialiased selection:bg-blue-600 selection:text-white transition-colors duration-200">
    {{-- Form Column: Accessible HTML5 Main Landmark --}}
    <main class="w-full lg:w-1/2 min-h-dvh flex flex-col items-center justify-center p-6 sm:p-8 lg:p-10 relative z-10">
        {{-- Theme Mode Switcher (Light / Dark / System) --}}
        <div class="absolute top-4 right-4 sm:top-6 sm:right-6 z-20">
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

        <div class="w-full max-w-md space-y-5 my-auto py-6">
            <div class="text-center">
                <flux:brand href="/" logo="{{ asset('images/logo.png') }}" name="TUTUT" class="justify-center font-bold text-lg" />
            </div>

            {{-- Role Selection segmented tabs outside the card --}}
            <flux:tabs variant="segmented" class="w-full" aria-label="Portal Selection">
                <flux:tab href="{{ route('student.login') }}" wire:navigate.hover :current="request()->routeIs('student.*')" icon="academic-cap">Student</flux:tab>
                <flux:tab href="{{ route('admin.login') }}" wire:navigate.hover :current="request()->routeIs('admin.*')" icon="shield-check">Admin</flux:tab>
            </flux:tabs>

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
                    @if (session('error'))
                        <flux:callout variant="danger" icon="exclamation-triangle" role="alert" aria-live="assertive">
                            {{ session('error') }}
                        </flux:callout>
                    @endif

                    @if (session('success'))
                        <flux:callout variant="success" icon="check-circle" role="status" aria-live="polite">
                            {{ session('success') }}
                        </flux:callout>
                    @endif

                    @if ($errors->any())
                        <flux:callout variant="danger" icon="exclamation-triangle" role="alert" aria-live="assertive">
                            {{ $errors->first() }}
                        </flux:callout>
                    @endif

                    @yield('content')
                </flux:card.body>

                {{-- Outer Container: Footer --}}
                @hasSection('footer')
                    <flux:card.footer>
                        @yield('footer')
                    </flux:card.footer>
                @endif
            </flux:card>

            <flux:text size="xs" class="text-center text-zinc-500 dark:text-zinc-400">
                &copy; {{ date('Y') }} TUTUT. All rights reserved.
            </flux:text>
        </div>
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
