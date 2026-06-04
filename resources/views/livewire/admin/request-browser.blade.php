<div class="safe-top">
    {{-- Header --}}
    <div class="sticky top-0 z-10 bg-white border-b border-gray-200">
        <div class="flex items-center gap-3 px-4 py-4">
            <h1 class="flex-1 text-lg font-semibold text-ink">All Requests</h1>
            <button
                wire:click="$toggle('showFilters')"
                class="flex items-center gap-1.5 rounded-xl border px-3 py-2 text-sm font-medium transition
                    {{ $showFilters ? 'border-primary bg-primary/5 text-primary' : 'border-gray-300 text-ink-muted hover:bg-gray-50' }}"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75"/></svg>
                Filters
                @if($this->hasActiveFilters())
                    <span class="flex h-4 w-4 items-center justify-center rounded-full bg-primary text-[10px] font-bold text-white">!</span>
                @endif
            </button>
        </div>

        {{-- Search --}}
        <div class="px-4 pb-3 relative">
            <svg class="absolute left-7 top-1/2 -translate-y-1/2 h-4 w-4 text-ink-muted pointer-events-none"
                fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
            </svg>
            <input
                wire:model.live.debounce.300ms="search"
                type="search"
                placeholder="Case, MRN, lab number…"
                class="block w-full rounded-xl border border-gray-300 py-2.5 pl-9 pr-4 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
            >
        </div>

        {{-- Filter panel --}}
        @if($showFilters)
            <div class="border-t border-gray-100 px-4 py-3 space-y-3 bg-gray-50">
                <div class="grid grid-cols-2 gap-2">
                    <x-form.select wire:model.live="filterStatus" label="Status">
                        <option value="">All statuses</option>
                        @foreach($statuses as $s)
                            <option value="{{ $s->value }}">{{ $s->label() }}</option>
                        @endforeach
                    </x-form.select>
                    <x-form.select wire:model.live="filterPriority" label="Priority">
                        <option value="">All priorities</option>
                        @foreach($priorities as $p)
                            <option value="{{ $p->value }}">{{ $p->label() }}</option>
                        @endforeach
                    </x-form.select>
                    <x-form.select wire:model.live="filterType" label="Type">
                        <option value="">All types</option>
                        @foreach($types as $t)
                            <option value="{{ $t->value }}">{{ $t->shortLabel() }}</option>
                        @endforeach
                    </x-form.select>
                    <x-form.select wire:model.live="filterDoctor" label="Doctor">
                        <option value="">All doctors</option>
                        @foreach($doctors as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </x-form.select>
                    <x-form.input wire:model.live="dateFrom" type="date" label="From" />
                    <x-form.input wire:model.live="dateTo" type="date" label="To" />
                </div>
                @if($this->hasActiveFilters())
                    <button wire:click="clearFilters" class="text-xs text-red-500 hover:underline">Clear all filters</button>
                @endif
            </div>
        @endif
    </div>

    <div class="px-4 py-3 space-y-2">
        <p class="text-xs text-ink-muted">{{ $requests->total() }} {{ Str::plural('result', $requests->total()) }}</p>

        @forelse($requests as $request)
            <a href="{{ route('admin.requests.show', $request->ulid) }}" wire:navigate
               class="block rounded-2xl bg-white border shadow-card overflow-hidden hover:shadow-md transition active:scale-[0.99]
                   {{ $request->isStat() ? 'border-red-200' : 'border-gray-200' }}">
                @if($request->isStat())
                    <div class="bg-red-600 px-3 py-0.5 text-[10px] font-bold text-white tracking-widest">STAT</div>
                @endif
                <div class="p-3">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm font-semibold text-ink">{{ $request->type->shortLabel() }}</span>
                                @if($request->priority->value === 'urgent')
                                    <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-700">URGENT</span>
                                @endif
                            </div>
                            <p class="text-xs text-ink-muted mt-0.5">Case {{ $request->case_number }}
                                @if($request->mrn) · {{ $request->mrn }} @endif
                            </p>
                            <p class="text-xs text-ink-muted">
                                Dr. {{ $request->doctor->name }}
                                @if($request->assignedTech) · Tech: {{ $request->assignedTech->name }} @endif
                            </p>
                        </div>
                        <div class="flex shrink-0 flex-col items-end gap-1.5">
                            <x-status-badge :status="$request->status" />
                            <span class="text-xs text-ink-muted">{{ $request->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <p class="font-semibold text-ink">No requests found</p>
                <p class="mt-1 text-sm text-ink-muted">
                    @if($this->hasActiveFilters()) Try clearing your filters. @else No requests have been submitted yet. @endif
                </p>
            </div>
        @endforelse
    </div>

    <div class="px-4 pb-6">{{ $requests->links() }}</div>
</div>
