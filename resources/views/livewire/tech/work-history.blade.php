<div class="safe-top">
    <div class="sticky top-0 z-10 bg-white border-b border-gray-200 px-4 py-4">
        <h1 class="text-lg font-semibold text-ink">Fulfillment History</h1>
        <div class="mt-3 relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-ink-muted pointer-events-none"
                fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
            </svg>
            <input
                wire:model.live.debounce.300ms="search"
                type="search"
                placeholder="Search case, MRN, lab number…"
                class="block w-full rounded-xl border border-gray-300 py-2.5 pl-9 pr-4 text-sm text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
            >
        </div>
    </div>

    <div class="px-4 py-3 space-y-2">
        @forelse($requests as $request)
            <div class="rounded-2xl bg-white border border-gray-200 shadow-card p-4">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-ink">{{ $request->type->shortLabel() }}</p>
                        <p class="text-xs text-ink-muted">Case {{ $request->case_number }}</p>
                        @if($request->mrn) <p class="text-xs text-ink-muted">MRN: {{ $request->mrn }}</p> @endif
                        <p class="text-xs text-ink-muted mt-1">
                            Dr. {{ $request->doctor->name }} · {{ $request->updated_at->format('d M Y') }}
                        </p>
                    </div>
                    <x-status-badge :status="$request->status" />
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <p class="font-semibold text-ink">No history yet</p>
                <p class="mt-1 text-sm text-ink-muted">Completed and cancelled requests appear here.</p>
            </div>
        @endforelse
    </div>

    <div class="px-4 pb-6">{{ $requests->links() }}</div>
</div>
