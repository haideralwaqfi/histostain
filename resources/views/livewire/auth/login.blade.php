<div>
    <h2 class="mb-6 text-center text-xl font-semibold text-ink">Sign in</h2>

    <form wire:submit="login" class="space-y-4" novalidate>
        <div>
            <label for="email" class="block text-sm font-medium text-ink">Email</label>
            <input
                wire:model="email"
                id="email"
                type="email"
                autocomplete="email"
                class="mt-1 block w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-ink shadow-sm placeholder-ink-muted focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary @error('email') border-red-400 @enderror"
                placeholder="you@hospital.org"
            >
            @error('email')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-ink">Password</label>
            <input
                wire:model="password"
                id="password"
                type="password"
                autocomplete="current-password"
                class="mt-1 block w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-ink shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
            >
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm text-ink-muted">
                <input wire:model="remember" type="checkbox" class="rounded border-gray-300 text-primary">
                Remember me
            </label>
        </div>

        <button
            type="submit"
            class="w-full rounded-xl bg-primary px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark active:scale-[0.98] min-h-[48px]"
        >
            <span wire:loading.remove>Sign in</span>
            <span wire:loading>Signing in…</span>
        </button>
    </form>

    <div class="mt-4">
        <div class="relative flex items-center">
            <div class="flex-grow border-t border-gray-200"></div>
            <span class="mx-3 text-xs text-ink-muted">or</span>
            <div class="flex-grow border-t border-gray-200"></div>
        </div>
        <a
            href="{{ route('auth.google') }}"
            class="mt-3 flex w-full items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-ink shadow-sm transition hover:bg-gray-50 active:scale-[0.98] min-h-[48px]"
        >
            <svg class="h-5 w-5" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
            Continue with Google
        </a>
    </div>

    <p class="mt-6 text-center text-sm text-ink-muted">
        No account?
        <a href="{{ route('register') }}" wire:navigate class="font-medium text-primary hover:underline">Register</a>
    </p>

    <p class="mt-8 text-center text-xs text-ink-muted">
        &copy; {{ date('Y') }} Developed &amp; designed by
        <a href="https://www.linkedin.com/in/haider-al-waqfi-pmp-itil-v4-76149480/"
           target="_blank"
           rel="noopener noreferrer"
           class="font-medium text-primary hover:underline">Haider Alwaqfi</a>
    </p>
</div>
