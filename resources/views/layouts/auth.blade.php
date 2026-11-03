<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <title>{{ $title ?? 'Log In' }} — TUTUT</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @fluxAppearance
</head>
<body class="min-h-dvh bg-zinc-50 dark:bg-zinc-900 antialiased selection:bg-blue-600 selection:text-white">
    {{-- Form Column: Natural page flow on the left --}}
    <div class="w-full lg:w-1/2 min-h-dvh flex flex-col items-center justify-center p-6 sm:p-8 lg:p-10 relative z-10">
        <div class="w-full max-w-md space-y-5 my-auto py-6">
            <div class="text-center">
                <flux:brand href="/" logo="{{ asset('images/logo.png') }}" name="TUTUT" class="justify-center font-bold text-lg" />
            </div>

            {{-- Role Selection segmented tabs outside the card --}}
            <flux:tabs variant="segmented" class="w-full">
                <flux:tab href="{{ route('student.login') }}" wire:navigate.hover :current="request()->routeIs('student.*')" icon="academic-cap">Student</flux:tab>
                <flux:tab href="{{ route('admin.login') }}" wire:navigate.hover :current="request()->routeIs('admin.*')" icon="shield-check">Admin</flux:tab>
            </flux:tabs>

            {{-- Main Inset Card --}}
            <flux:card body="inset" variant="soft" size="lg">
                {{-- Outer Container: Centered Greeting Header --}}
                <flux:card.header class="flex flex-col items-center text-center">
                    <flux:card.heading size="lg" class="text-center">{{ $heading ?? 'Welcome Back' }}</flux:card.heading>
                    @if (isset($subheading))
                        <flux:card.subheading class="text-center mt-1">{{ $subheading }}</flux:card.subheading>
                    @endif
                </flux:card.header>

                {{-- Inner Container: Recessed Inset Body --}}
                <flux:card.body class="space-y-6">
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
    </div>

    {{-- Fixed Inset Framed Image Column (Large Screens Only: Fixed Position) --}}
    <div class="hidden lg:block fixed top-0 right-0 w-1/2 h-dvh p-3 z-0 pointer-events-none">
        <div class="relative w-full h-full rounded-2xl overflow-hidden border border-zinc-200 dark:border-zinc-800 shadow-xs bg-zinc-900 pointer-events-auto">
            <img
                src="https://images.unsplash.com/photo-1606326608726-07a8ee903449?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=NHwxMjA3fDA%3D"
                alt="TUTUT Examination Campus"
                class="absolute inset-0 h-full w-full object-cover object-center"
                loading="eager"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-zinc-950/60 via-transparent to-transparent"></div>
        </div>
    </div>

    @livewireScripts
    @fluxScripts
</body>
</html>
