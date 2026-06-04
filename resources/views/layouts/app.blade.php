<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0F6D8E">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="vapid-public-key" content="{{ config('webpush.vapid.public_key', '') }}">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <title>{{ config('app.name', 'HistoStains') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body
    class="h-full bg-surface font-sans antialiased"
    x-data="{
        toasts: [],
        installable: false,
        addToast(msg, type = 'info') {
            const id = Date.now();
            this.toasts.push({ id, msg, type });
            setTimeout(() => this.removeToast(id), 3500);
        },
        removeToast(id) { this.toasts = this.toasts.filter(t => t.id !== id); },
        async installPwa() {
            const ok = await window.PwaHelper?.install();
            if (ok) this.installable = false;
        }
    }"
    @toast.window="addToast($event.detail.message, $event.detail.type)"
    x-on:pwa-installable.document="installable = true"
    x-on:pwa-installed.document="installable = false"
>
    <!-- PWA install banner -->
    <div
        x-show="installable"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="-translate-y-full opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        class="fixed inset-x-0 top-0 z-50 flex items-center justify-between gap-3 bg-primary px-4 py-3 safe-top shadow-lg"
    >
        <div class="flex items-center gap-2">
            <svg class="h-5 w-5 shrink-0 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
            <p class="text-sm font-medium text-white">Install HistoStains for quick access</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="installPwa()" class="rounded-lg bg-white px-3 py-1.5 text-xs font-semibold text-primary">
                Install
            </button>
            <button @click="installable = false" class="text-white/70 hover:text-white">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    <!-- Main content area — pb accounts for bottom nav + safe area -->
    <div class="flex h-full flex-col">
        <main class="flex-1 overflow-y-auto pb-[calc(3.5rem+env(safe-area-inset-bottom))]">
            {{ $slot }}
        </main>

        @auth
            @include('components.bottom-nav')
        @endauth
    </div>

    <!-- Toast stack -->
    <div
        class="pointer-events-none fixed inset-x-0 top-4 z-50 flex flex-col items-center gap-2 px-4"
        aria-live="polite"
    >
        <template x-for="toast in toasts" :key="toast.id">
            <div
                x-show="true"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="pointer-events-auto flex max-w-sm items-center gap-3 rounded-xl px-4 py-3 shadow-lg text-sm font-medium"
                :class="{
                    'bg-green-600 text-white': toast.type === 'success',
                    'bg-amber-500 text-white': toast.type === 'warning',
                    'bg-red-600 text-white':   toast.type === 'error',
                    'bg-ink text-white':        toast.type === 'info',
                }"
            >
                <span x-text="toast.msg"></span>
                <button @click="removeToast(toast.id)" class="ml-auto opacity-70 hover:opacity-100">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </template>
    </div>

    @livewireScripts

    {{-- Bridge SW postMessage('push-received') → Livewire event so the badge refreshes --}}
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.addEventListener('message', (event) => {
                if (event.data?.type === 'push-received') {
                    window.dispatchEvent(new CustomEvent('push-received'));
                }
            });
        }
    </script>
</body>
</html>
