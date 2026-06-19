<div class="safe-top">
    {{-- Header --}}
    <div class="sticky top-0 z-10 bg-white border-b border-gray-200 px-4 py-4">
        <div class="flex items-center justify-between gap-3">
            <h1 class="text-lg font-semibold text-ink">Incoming Queue</h1>
            <div class="flex items-center gap-3">
                <span class="text-sm text-ink-muted">{{ $requests->total() }} pending</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-semibold text-ink-muted transition hover:bg-gray-50">
                        Logout
                    </button>
                </form>
            </div>
        </div>
        {{-- Type filter --}}
        <div class="flex gap-2 mt-3 overflow-x-auto pb-1 -mx-1 px-1">
            <button wire:click="filterBy('')"
                wire:loading.class="opacity-50 pointer-events-none"
                wire:target="filterBy"
                class="shrink-0 rounded-full px-3 py-1 text-xs font-medium transition {{ $filterType === '' ? 'bg-primary text-white' : 'bg-gray-100 text-ink-muted hover:bg-gray-200' }}">
                All types
            </button>
            @foreach($typeOptions as $t)
                <button wire:click="filterBy('{{ $t->value }}')"
                    wire:loading.class="opacity-50 pointer-events-none"
                    wire:target="filterBy"
                    class="shrink-0 rounded-full px-3 py-1 text-xs font-medium transition {{ $filterType === $t->value ? 'bg-primary text-white' : 'bg-gray-100 text-ink-muted hover:bg-gray-200' }}">
                    {{ $t->shortLabel() }}
                </button>
            @endforeach
        </div>
    </div>

    <div class="px-4 py-3 space-y-3">
        @forelse($requests as $request)
            <div x-data="{ open: false }"
                class="rounded-2xl bg-white border shadow-card overflow-hidden
                    {{ $request->isStat() ? 'border-red-300 stat-ring' : 'border-gray-200' }}">

                {{-- STAT banner --}}
                @if($request->isStat())
                    <div class="flex items-center gap-2 bg-red-600 px-4 py-1.5">
                        <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                        <span class="text-xs font-bold text-white tracking-widest">STAT — IMMEDIATE ACTION REQUIRED</span>
                    </div>
                @endif

                <div class="p-4">
                    {{-- Request summary --}}
                    <div class="flex items-start justify-between gap-2 mb-3">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm font-semibold text-ink">{{ $request->type->label() }}</span>
                                @if($request->priority->value === 'urgent')
                                    <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-700">Urgent</span>
                                @endif
                            </div>
                            <p class="text-xs text-ink-muted mt-0.5">Case {{ $request->case_number }}</p>
                            @if($request->mrn)
                                <p class="text-xs text-ink-muted">MRN: {{ $request->mrn }}</p>
                            @endif
                            <p class="text-xs text-ink-muted">
                                Dr. {{ $request->doctor->name }} · {{ $request->created_at->format('d M Y, g:i A') }}
                            </p>
                        </div>
                        @if(isset($request->type_data['blocks']))
                            <span class="shrink-0 rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-ink-muted">
                                {{ count($request->type_data['blocks']) }} block(s)
                            </span>
                        @endif
                    </div>

                    {{-- Details toggle --}}
                    <button @click="open = !open"
                        class="flex w-full items-center justify-between rounded-xl border border-gray-200 px-3 py-2 text-xs font-medium text-ink-muted transition hover:bg-gray-50 mb-3">
                        <span x-text="open ? 'Hide details' : 'View stain details'"></span>
                        <svg class="h-4 w-4 transition-transform duration-200"
                            :class="open ? 'rotate-180' : ''"
                            fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                        </svg>
                    </button>

                    {{-- Stain details panel --}}
                    <div x-show="open"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="mb-3">
                        <x-stain-type-details :request="$request" />
                    </div>

                    {{-- Accept button --}}
                    <button
                        wire:click="accept({{ $request->id }})"
                        wire:loading.attr="disabled"
                        wire:target="accept({{ $request->id }})"
                        class="w-full rounded-xl py-3 text-sm font-semibold transition min-h-12 active:scale-[0.98] disabled:opacity-60
                            {{ $request->isStat() ? 'bg-red-600 text-white hover:bg-red-700' : 'bg-primary text-white hover:bg-primary-dark' }}"
                    >
                        <span wire:loading.remove wire:target="accept({{ $request->id }})">Accept</span>
                        <span wire:loading wire:target="accept({{ $request->id }})">Accepting…</span>
                    </button>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-green-100">
                    <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="mt-3 font-semibold text-ink">Queue is clear</p>
                <p class="mt-1 text-sm text-ink-muted">No pending requests at this time.</p>
            </div>
        @endforelse
    </div>

    <div class="px-4 pb-6">{{ $requests->links() }}</div>
</div>
