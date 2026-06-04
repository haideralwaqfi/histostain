// HistoStains — PWA + Push notification frontend

// ─── Service Worker ──────────────────────────────────────────────────────────

let _swRegistration = null;

if ('serviceWorker' in navigator) {
    window.addEventListener('load', async () => {
        try {
            _swRegistration = await navigator.serviceWorker.register('/sw.js', { scope: '/' });
        } catch (err) {
            console.warn('[SW] Registration failed:', err);
        }
    });
}

// ─── PWA install prompt ──────────────────────────────────────────────────────

let _deferredInstallPrompt = null;

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    _deferredInstallPrompt = e;
    document.dispatchEvent(new CustomEvent('pwa-installable'));
});

window.addEventListener('appinstalled', () => {
    _deferredInstallPrompt = null;
    document.dispatchEvent(new CustomEvent('pwa-installed'));
});

window.PwaHelper = {
    isInstallable: () => !!_deferredInstallPrompt,

    async install() {
        if (!_deferredInstallPrompt) return false;
        _deferredInstallPrompt.prompt();
        const { outcome } = await _deferredInstallPrompt.userChoice;
        _deferredInstallPrompt = null;
        return outcome === 'accepted';
    },
};

// ─── Web Push helper ─────────────────────────────────────────────────────────

window.PushHelper = {
    isSupported() {
        return (
            'serviceWorker' in navigator &&
            'PushManager' in window &&
            'Notification' in window
        );
    },

    permission() {
        return Notification.permission; // 'default' | 'granted' | 'denied'
    },

    async isSubscribed() {
        if (!this.isSupported()) return false;
        try {
            const reg = await navigator.serviceWorker.ready;
            const sub = await reg.pushManager.getSubscription();
            return !!sub && Notification.permission === 'granted';
        } catch {
            return false;
        }
    },

    // Subscribe and persist the subscription on the server.
    // Returns { ok: true } or { ok: false, reason: string }
    async subscribe() {
        if (!this.isSupported()) return { ok: false, reason: 'unsupported' };

        const permission = await Notification.requestPermission();
        if (permission !== 'granted') return { ok: false, reason: 'denied' };

        try {
            const reg = await navigator.serviceWorker.ready;
            const vapidKey = document
                .querySelector('meta[name="vapid-public-key"]')
                ?.getAttribute('content');

            if (!vapidKey) return { ok: false, reason: 'no-vapid-key' };

            const subscription = await reg.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: _urlBase64ToUint8Array(vapidKey),
            });

            const res = await _authedFetch('POST', '/push-subscriptions', subscription.toJSON());
            if (!res.ok) throw new Error(`Server error ${res.status}`);

            return { ok: true };
        } catch (err) {
            console.warn('[Push] Subscribe failed:', err);
            return { ok: false, reason: 'error', message: err.message };
        }
    },

    async unsubscribe() {
        try {
            const reg = await navigator.serviceWorker.ready;
            const sub = await reg.pushManager.getSubscription();
            if (!sub) return;
            await sub.unsubscribe();
            await _authedFetch('DELETE', '/push-subscriptions', { endpoint: sub.endpoint });
        } catch (err) {
            console.warn('[Push] Unsubscribe failed:', err);
        }
    },
};

// ─── Expo push token registration ────────────────────────────────────────────
// Called by a React Native / Expo app via a WebView bridge or deep-link.
// The token is posted to the server so the Laravel Expo channel can use it.

window.registerExpoPushToken = async (token) => {
    if (!token?.startsWith('ExponentPushToken[')) return;
    await _authedFetch('POST', '/api/expo-push-token', { token });
};

// ─── Helpers ─────────────────────────────────────────────────────────────────

function _authedFetch(method, url, body) {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
    return fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify(body),
    });
}

function _urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const raw = atob(base64);
    const output = new Uint8Array(raw.length);
    for (let i = 0; i < raw.length; i++) output[i] = raw.charCodeAt(i);
    return output;
}
