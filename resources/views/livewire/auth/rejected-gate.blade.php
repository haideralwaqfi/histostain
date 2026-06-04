<div class="text-center">
    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-100">
        <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
        </svg>
    </div>

    <h2 class="mt-4 text-xl font-semibold text-ink">Registration not approved</h2>
    <p class="mt-2 text-sm text-ink-muted leading-relaxed">
        Your account registration was reviewed and could not be approved at this time.
    </p>

    @if($reason)
        <div class="mt-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-left">
            <p class="text-xs font-medium uppercase tracking-wide text-red-600">Reason provided</p>
            <p class="mt-1 text-sm text-red-800">{{ $reason }}</p>
        </div>
    @endif

    <p class="mt-4 text-sm text-ink-muted">
        If you believe this is an error, please contact your lab administrator.
    </p>

    <button
        wire:click="logout"
        class="mt-6 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-ink-muted shadow-sm transition hover:bg-gray-50 active:scale-[0.98] min-h-[48px]"
    >
        Sign out
    </button>
</div>
