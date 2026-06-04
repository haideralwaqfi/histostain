<div class="safe-top">
    {{-- Header --}}
    <div class="sticky top-0 z-10 bg-white border-b border-gray-200 px-4 py-4">
        <div class="flex items-center justify-between">
            <h1 class="text-lg font-semibold text-ink">My Requests</h1>
            <a href="{{ route('doctor.requests.create') }}" wire:navigate
               class="flex items-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-sm font-semibold text-white min-h-10">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                New
            </a>
        </div>
        {{-- Status filter chips --}}
        <div class="flex gap-2 mt-3 overflow-x-auto pb-1 -mx-1 px-1">
            <button wire:click="$set('filterStatus', '')"
                class="shrink-0 rounded-full px-3 py-1 text-xs font-medium transition {{ $filterStatus === '' ? 'bg-primary text-white' : 'bg-gray-100 text-ink-muted hover:bg-gray-200' }}">
                All
            </button>
            @foreach($statuses as $s)
                <button wire:click="$set('filterStatus', '{{ $s->value }}')"
                    class="shrink-0 rounded-full px-3 py-1 text-xs font-medium transition {{ $filterStatus === $s->value ? 'bg-primary text-white' : 'bg-gray-100 text-ink-muted hover:bg-gray-200' }}">
                    {{ $s->label() }}
                </button>
            @endforeach
        </div>
    </div>

    <div class="px-4 py-3 space-y-3">
        @forelse($requests as $request)
            <a href="{{ route('doctor.requests.show', $request->ulid) }}" wire:navigate
               class="block rounded-2xl bg-white border shadow-card overflow-hidden transition hover:shadow-md active:scale-[0.99]
                   {{ $request->isStat() ? 'border-red-200 stat-ring' : 'border-gray-200' }}">
                {{-- STAT banner --}}
                @if($request->isStat())
                    <div class="bg-red-600 px-4 py-1 text-xs font-bold text-white tracking-widest">STAT</div>
                @endif
                <div class="p-4">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm font-semibold text-ink">{{ $request->type->shortLabel() }}</span>
                                @if(!$request->isStat() && $request->priority->value === 'urgent')
                                    <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-700">Urgent</span>
                                @endif
                            </div>
                            <p class="text-xs text-ink-muted mt-0.5">Case {{ $request->case_number }}</p>
                            @if($request->mrn)
                                <p class="text-xs text-ink-muted">MRN: {{ $request->mrn }}</p>
                            @endif
                        </div>
                        <x-status-badge :status="$request->status" />
                    </div>
                    <div class="mt-2 flex items-center justify-between text-xs text-ink-muted">
                        <span>{{ $request->created_at->diffForHumans() }}</span>
                        @if($request->assignedTech)
                            <span>Tech: {{ $request->assignedTech->name }}</span>
                        @endif
                    </div>
                </div>
            </a>
        @empty
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                    <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                </div>
                <p class="mt-3 font-semibold text-ink">No requests yet</p>
                <p class="mt-1 text-sm text-ink-muted">Create your first stain request.</p>
                <a href="{{ route('doctor.requests.create') }}" wire:navigate
                   class="mt-4 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white">
                    New request
                </a>
            </div>
        @endforelse
    </div>

    <div class="px-4 pb-6">{{ $requests->links() }}</div>
</div>
