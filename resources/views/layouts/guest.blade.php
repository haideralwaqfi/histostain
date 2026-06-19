<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0F6D8E">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <title>{{ config('app.name', 'HistoStains') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full bg-surface font-sans antialiased">
    <div class="flex min-h-full flex-col items-center justify-center px-4 py-12 safe-top safe-bottom">
        <!-- Logo -->
        <div class="mb-8 text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-primary shadow-lg">
                <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23-.693L5 14.5m14.8.8l1.402 1.402c1 1 .03 2.688-1.379 2.688H4.176c-1.407 0-2.378-1.688-1.379-2.688L4.2 15.3" />
                </svg>
            </div>
            <h1 class="mt-3 text-2xl font-bold tracking-tight text-ink">HistoStains</h1>
            <p class="text-sm text-ink-muted">Pathology Lab Request System</p>
        </div>

        <!-- Card -->
        <div class="w-full max-w-sm rounded-2xl bg-white px-6 py-8 shadow-card">
            {{ $slot }}
        </div>

        <!-- Credit -->
        <p class="mt-6 text-center text-xs text-ink-muted">
            &copy; {{ date('Y') }} Developed &amp; designed by
            <a href="https://www.linkedin.com/in/haider-al-waqfi-pmp-itil-v4-76149480/"
               target="_blank"
               rel="noopener noreferrer"
               class="font-medium text-primary hover:underline">Haider Alwaqfi</a>
        </p>
    </div>

    @livewireScripts
</body>
</html>
