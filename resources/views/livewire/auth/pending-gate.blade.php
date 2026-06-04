<div class="text-center">
    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-amber-100">
        <svg class="h-8 w-8 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </div>

    <h2 class="mt-4 text-xl font-semibold text-ink">Account pending approval</h2>
    <p class="mt-2 text-sm text-ink-muted leading-relaxed">
        Your registration has been received. A lab administrator will review your account
        and you will be notified once approved.
    </p>

    <div class="mt-6 rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-800">
        If you need urgent access, please contact your lab administrator directly.
    </div>

    <button
        wire:click="logout"
        class="mt-6 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-ink-muted shadow-sm transition hover:bg-gray-50 active:scale-[0.98] min-h-[48px]"
    >
        Sign out
    </button>
</div>
