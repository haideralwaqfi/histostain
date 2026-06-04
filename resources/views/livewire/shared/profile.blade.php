<div class="safe-top">
    <div class="sticky top-0 z-10 bg-white border-b border-gray-200 px-4 py-4">
        <h1 class="text-lg font-semibold text-ink">Profile</h1>
    </div>

    <div class="px-4 py-5 space-y-5">

        {{-- Avatar + identity --}}
        <div class="flex items-center gap-4 rounded-2xl border border-gray-200 bg-white px-4 py-5">
            @if($user->avatar)
                <img src="{{ $user->avatar }}" alt="" class="h-16 w-16 rounded-full object-cover shrink-0">
            @else
                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary text-2xl font-bold">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif
            <div class="min-w-0">
                <p class="font-semibold text-ink">{{ $user->name }}</p>
                <p class="text-sm text-ink-muted truncate">{{ $user->email }}</p>
                @if($user->role)
                    <span class="mt-1 inline-block rounded-full bg-primary/10 px-2.5 py-0.5 text-xs font-semibold text-primary">
                        {{ $user->role->label() }}
                    </span>
                @endif
                @if($user->google_id)
                    <p class="mt-1 flex items-center gap-1 text-xs text-ink-muted">
                        <svg class="h-3 w-3" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                        Linked with Google
                    </p>
                @endif
            </div>
        </div>

        {{-- Edit name / email --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-4 space-y-3">
            <h2 class="text-sm font-semibold text-ink-muted uppercase tracking-wide">Account Details</h2>
            <x-form.input wire:model="name" label="Full name" required />
            @error('name') <x-form.error>{{ $message }}</x-form.error> @enderror
            <x-form.input wire:model="email" label="Email" type="email" required />
            @error('email') <x-form.error>{{ $message }}</x-form.error> @enderror
            <button wire:click="saveProfile"
                class="w-full rounded-xl bg-primary py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark active:scale-[0.98] min-h-12">
                <span wire:loading.remove wire:target="saveProfile">Save changes</span>
                <span wire:loading wire:target="saveProfile">Saving…</span>
            </button>
        </div>

        {{-- Push notifications toggle --}}
        <div
            class="rounded-2xl border border-gray-200 bg-white p-4"
            x-data="{
                supported: false,
                subscribed: false,
                denied: false,
                loading: true,
                async init() {
                    if (!window.PushHelper?.isSupported()) { this.loading = false; return; }
                    this.supported = true;
                    this.denied = (window.PushHelper.permission() === 'denied');
                    this.subscribed = await window.PushHelper.isSubscribed();
                    this.loading = false;
                },
                async toggle() {
                    this.loading = true;
                    if (this.subscribed) {
                        await window.PushHelper.unsubscribe();
                        this.subscribed = false;
                        $wire.pushStatusChanged(false);
                    } else {
                        const result = await window.PushHelper.subscribe();
                        if (result.ok) {
                            this.subscribed = true;
                            $wire.pushStatusChanged(true);
                        } else if (result.reason === 'denied') {
                            this.denied = true;
                        }
                    }
                    this.loading = false;
                }
            }"
        >
            <h2 class="text-sm font-semibold text-ink-muted uppercase tracking-wide mb-3">Push Notifications</h2>

            {{-- Not supported --}}
            <template x-if="!loading && !supported">
                <p class="text-sm text-ink-muted">Push notifications are not supported by this browser.</p>
            </template>

            {{-- Permission denied --}}
            <template x-if="!loading && supported && denied">
                <div class="rounded-xl bg-amber-50 border border-amber-200 p-3 text-sm text-amber-700">
                    Notifications are blocked in your browser settings. To enable them, update the site's permissions in your browser and reload.
                </div>
            </template>

            {{-- Toggle --}}
            <template x-if="!loading && supported && !denied">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-ink" x-text="subscribed ? 'Push notifications on' : 'Push notifications off'"></p>
                        <p class="text-xs text-ink-muted" x-text="subscribed ? 'You\'ll be notified of updates to your requests.' : 'Enable to get browser alerts for request updates.'"></p>
                    </div>
                    <button
                        @click="toggle()"
                        :disabled="loading"
                        class="relative inline-flex h-7 w-12 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:opacity-50"
                        :class="subscribed ? 'bg-primary' : 'bg-gray-200'"
                        role="switch"
                        :aria-checked="subscribed"
                    >
                        <span
                            class="pointer-events-none inline-block h-6 w-6 rounded-full bg-white shadow ring-0 transition duration-200"
                            :class="subscribed ? 'translate-x-5' : 'translate-x-0'"
                        ></span>
                    </button>
                </div>
            </template>

            {{-- Loading skeleton --}}
            <template x-if="loading">
                <div class="flex items-center gap-3">
                    <div class="h-4 w-32 animate-pulse rounded bg-gray-200"></div>
                    <div class="ml-auto h-7 w-12 animate-pulse rounded-full bg-gray-200"></div>
                </div>
            </template>
        </div>

        {{-- Change password (only for non-Google accounts) --}}
        @if($user->password)
            <div class="rounded-2xl border border-gray-200 bg-white p-4 space-y-3">
                <h2 class="text-sm font-semibold text-ink-muted uppercase tracking-wide">Change Password</h2>
                <x-form.input wire:model="currentPassword" label="Current password" type="password" />
                @error('currentPassword') <x-form.error>{{ $message }}</x-form.error> @enderror
                <x-form.input wire:model="newPassword" label="New password" type="password" placeholder="Min. 8 characters" />
                @error('newPassword') <x-form.error>{{ $message }}</x-form.error> @enderror
                <x-form.input wire:model="newPasswordConfirmation" label="Confirm new password" type="password" />
                <button wire:click="savePassword"
                    class="w-full rounded-xl bg-ink py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-ink/90 active:scale-[0.98] min-h-12">
                    <span wire:loading.remove wire:target="savePassword">Change password</span>
                    <span wire:loading wire:target="savePassword">Updating…</span>
                </button>
            </div>
        @else
            <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
                <p class="text-sm text-ink-muted">Password management is handled by Google for your account.</p>
            </div>
        @endif

        {{-- Sign out --}}
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                class="w-full rounded-xl border border-gray-300 py-3 text-sm font-medium text-ink-muted transition hover:bg-gray-50 min-h-12">
                Sign out
            </button>
        </form>

    </div>
</div>
