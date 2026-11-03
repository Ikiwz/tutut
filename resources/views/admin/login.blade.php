<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <title>Admin Login — TUTUT</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>
<body class="min-h-screen bg-zinc-50 dark:bg-zinc-900 flex items-center justify-center p-4 antialiased">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <img src="{{ asset('images/logo.png') }}" alt="TUTUT Logo" class="h-14 w-auto mx-auto mb-3">
            <flux:heading size="xl" level="1">TUTUT Admin Portal</flux:heading>
            <flux:text class="mt-1">Sign in with administrator credentials</flux:text>
        </div>

        <flux:card class="space-y-6">
            <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-4">
                @csrf

                <flux:field>
                    <flux:label>Email Address</flux:label>
                    <flux:input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="admin@toefl.com" icon="envelope" />
                    <flux:error name="email" />
                </flux:field>

                <flux:field>
                    <flux:label>Password</flux:label>
                    <flux:input type="password" name="password" required placeholder="••••••••" icon="key" />
                    <flux:error name="password" />
                </flux:field>

                <div class="flex items-center justify-between">
                    <flux:checkbox name="remember" label="Remember me" />
                </div>

                <flux:button type="submit" variant="primary" class="w-full">
                    Sign In to Dashboard
                </flux:button>
            </form>
        </flux:card>

        <p class="text-center text-xs text-zinc-500 dark:text-zinc-400 mt-6">
            &copy; {{ date('Y') }} TUTUT — TOEFL Examination Platform.
        </p>
    </div>

    @fluxScripts
</body>
</html>

