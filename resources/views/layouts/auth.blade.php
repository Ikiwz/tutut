<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <title>{{ $title ?? 'Sign In' }} — TUTUT</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @fluxAppearance
</head>
<body class="min-h-dvh bg-zinc-50 dark:bg-zinc-900 flex items-center justify-center p-6 antialiased selection:bg-blue-600 selection:text-white">
    <div class="w-full max-w-sm space-y-6">
        <div class="text-center">
            <flux:brand href="/" logo="{{ asset('images/logo.png') }}" name="TUTUT" class="justify-center font-bold text-lg" />
        </div>

        <flux:card class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $heading ?? 'Sign In' }}</flux:heading>
                <flux:subheading class="mt-1">{{ $subheading ?? 'Enter your credentials to continue.' }}</flux:subheading>
            </div>

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
        </flux:card>

        <flux:text size="xs" class="text-center text-zinc-500 dark:text-zinc-400">
            &copy; {{ date('Y') }} TUTUT. All rights reserved.
        </flux:text>
    </div>

    @livewireScripts
    @fluxScripts
</body>
</html>
