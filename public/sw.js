// HistoStains Service Worker
// Cache strategy: network-first for navigation, cache-first for static assets.

const CACHE = 'histostains-v2';
const PRECACHE = ['/', '/manifest.webmanifest', '/icons/icon-192.png', '/icons/icon-512.png'];

// ─── Lifecycle ───────────────────────────────────────────────────────────────

self.addEventListener('install', (e) => {
    e.waitUntil(caches.open(CACHE).then((c) => c.addAll(PRECACHE)));
    self.skipWaiting();
});

self.addEventListener('activate', (e) => {
    e.waitUntil(
        caches
            .keys()
            .then((keys) => Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k))))
    );
    self.clients.claim();
});

// ─── Fetch ───────────────────────────────────────────────────────────────────

self.addEventListener('fetch', (e) => {
    const { request } = e;
    const url = new URL(request.url);

    // Only intercept same-origin requests; leave Livewire AJAX alone
    if (url.origin !== location.origin) return;
    if (url.pathname.startsWith('/livewire')) return;
    if (url.pathname.startsWith('/api')) return;

    if (request.mode === 'navigate') {
        // Navigation: network-first, fallback to cached shell
        e.respondWith(
            fetch(request).catch(() => caches.match('/') ?? fetch(request))
        );
        return;
    }

    if (['script', 'style', 'image', 'font'].includes(request.destination)) {
        // Static assets: cache-first, populate on miss
        e.respondWith(
            caches.match(request).then(
                (cached) =>
                    cached ??
                    fetch(request).then((res) => {
                        if (res.ok) caches.open(CACHE).then((c) => c.put(request, res.clone()));
                        return res;
                    })
            )
        );
    }
});

// ─── Push notifications ──────────────────────────────────────────────────────

self.addEventListener('push', (e) => {
    let payload = {};
    try {
        payload = e.data?.json() ?? {};
    } catch {
        payload = { title: 'HistoStains', body: e.data?.text() ?? '' };
    }

    const title = payload.title ?? 'HistoStains';
    const options = {
        body:    payload.body    ?? '',
        icon:    payload.icon    ?? '/icons/icon-192.png',
        badge:   payload.badge   ?? '/icons/badge-72.png',
        tag:     payload.tag     ?? 'histostains',
        renotify: true,
        data:    { url: payload.data?.url ?? '/' },
        // Surface immediately — no silent pushes for clinical notifications
        requireInteraction: payload.requireInteraction ?? false,
    };

    // STAT requests get a persistent notification that requires interaction
    if (payload.data?.priority === 'stat' || title.includes('STAT')) {
        options.requireInteraction = true;
        options.vibrate = [200, 100, 200, 100, 200];
    }

    e.waitUntil(self.registration.showNotification(title, options));
});

// ─── Notification click ──────────────────────────────────────────────────────

self.addEventListener('notificationclick', (e) => {
    e.notification.close();
    const targetUrl = e.notification.data?.url ?? '/';

    e.waitUntil(
        clients
            .matchAll({ type: 'window', includeUncontrolled: true })
            .then((windowClients) => {
                // Focus existing tab if already open
                for (const client of windowClients) {
                    if (client.url === targetUrl && 'focus' in client) return client.focus();
                }
                return clients.openWindow(targetUrl);
            })
    );
});

// ─── Message passing ─────────────────────────────────────────────────────────
// When a push arrives the SW tells all open tabs to refresh their notification
// count so the badge updates without a page reload.

self.addEventListener('push', () => {
    clients.matchAll({ type: 'window' }).then((tabs) => {
        tabs.forEach((tab) => tab.postMessage({ type: 'push-received' }));
    });
});
