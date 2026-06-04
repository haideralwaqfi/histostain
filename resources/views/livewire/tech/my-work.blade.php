<div
    class="safe-top"
    x-data="{ onHoldOpen: @entangle('onHoldId').live, attachOpen: @entangle('attachingToId').live }"
>
    {{-- Header --}}
    <div class="sticky top-0 z-10 bg-white border-b border-gray-200 px-4 py-4">
        <h1 class="text-lg font-semibold text-ink">My Work</h1>
        <p class="text-sm text-ink-muted">{{ $requests->total() }} active request(s)</p>
    </div>

    <div class="px-4 py-3 space-y-3">
        @forelse($requests as $request)
            <div class="rounded-2xl bg-white border shadow-card overflow-hidden
                {{ $request->isStat() ? 'border-red-300 stat-ring' : 'border-gray-200' }}">

                @if($request->isStat())
                    <div class="bg-red-600 px-4 py-1 text-xs font-bold text-white tracking-widest">STAT</div>
                @endif

                <div class="p-4 space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-ink">{{ $request->type->label() }}</p>
                            <p class="text-xs text-ink-muted">Case {{ $request->case_number }}
                                @if($request->mrn) · MRN {{ $request->mrn }}@endif
                            </p>
                            <p class="text-xs text-ink-muted">Dr. {{ $request->doctor->name }}</p>
                        </div>
                        <x-status-badge :status="$request->status" />
                    </div>

                    {{-- On-hold reason display --}}
                    @if($request->status->value === 'on_hold' && $request->transitions->last()?->note)
                        <div class="rounded-lg bg-orange-50 border border-orange-200 px-3 py-2 text-xs text-orange-700">
                            <span class="font-semibold">Hold reason: </span>{{ $request->transitions->last()->note }}
                        </div>
                    @endif

                    {{-- Action buttons --}}
                    <div class="flex gap-2 flex-wrap">
                        @if($request->status->value === 'accepted')
                            <button wire:click="advance({{ $request->id }}, 'in_progress')"
                                class="flex-1 rounded-xl bg-indigo-600 py-2.5 text-xs font-semibold text-white min-h-11 transition active:scale-[0.98]">
                                Start Processing
                            </button>
                            <button wire:click="startOnHold({{ $request->id }})"
                                class="rounded-xl border border-orange-300 px-4 py-2.5 text-xs font-semibold text-orange-600 min-h-11 transition hover:bg-orange-50">
                                On Hold
                            </button>
                        @elseif($request->status->value === 'in_progress')
                            <button wire:click="advance({{ $request->id }}, 'completed')"
                                class="flex-1 rounded-xl bg-green-600 py-2.5 text-xs font-semibold text-white min-h-11 transition active:scale-[0.98]">
                                Mark Complete
                            </button>
                            <button wire:click="startOnHold({{ $request->id }})"
                                class="rounded-xl border border-orange-300 px-4 py-2.5 text-xs font-semibold text-orange-600 min-h-11 transition hover:bg-orange-50">
                                On Hold
                            </button>
                            <button wire:click="startAttach({{ $request->id }})"
                                class="rounded-xl border border-gray-300 px-4 py-2.5 text-xs font-semibold text-ink-muted min-h-11 transition hover:bg-gray-50">
                                Attach Result
                            </button>
                        @elseif($request->status->value === 'on_hold')
                            <button wire:click="advance({{ $request->id }}, 'in_progress')"
                                class="flex-1 rounded-xl bg-primary py-2.5 text-xs font-semibold text-white min-h-11 transition active:scale-[0.98]">
                                Resume
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                    <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="mt-3 font-semibold text-ink">Nothing in progress</p>
                <p class="mt-1 text-sm text-ink-muted">Accept requests from the queue to start.</p>
            </div>
        @endforelse
    </div>

    <div class="px-4 pb-6">{{ $requests->links() }}</div>

    {{-- ── On-hold modal ── --}}
    <div x-show="onHoldOpen" x-cloak
        class="fixed inset-0 z-50 flex items-end justify-center"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
    >
        <div class="absolute inset-0 bg-black/40" wire:click="cancelOnHold"></div>
        <div class="relative w-full max-w-lg rounded-t-2xl bg-white p-6 space-y-4 safe-bottom"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0"
        >
            <div class="mx-auto h-1 w-10 rounded-full bg-gray-200 -mt-2 mb-2"></div>
            <h3 class="text-base font-semibold text-ink">Place on hold</h3>

            <div>
                <label class="block text-sm font-medium text-ink mb-1">
                    Reason <span class="text-red-500">*</span>
                </label>
                <textarea
                    wire:model="holdReason"
                    rows="3"
                    placeholder="Why is this request being placed on hold?"
                    class="block w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary resize-none"
                ></textarea>
                @error('holdReason') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-ink mb-1">Additional note (optional)</label>
                <textarea wire:model="holdNote" rows="2"
                    class="block w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary resize-none"></textarea>
            </div>
            <div class="flex gap-3">
                <button wire:click="confirmOnHold"
                    class="flex-1 rounded-xl bg-orange-500 py-3 text-sm font-semibold text-white min-h-12 transition active:scale-[0.98]">
                    <span wire:loading.remove wire:target="confirmOnHold">Confirm Hold</span>
                    <span wire:loading wire:target="confirmOnHold">Saving…</span>
                </button>
                <button wire:click="cancelOnHold"
                    class="rounded-xl border border-gray-300 px-5 py-3 text-sm font-medium text-ink-muted min-h-12">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    {{-- ── Attach result modal ── --}}
    <div x-show="attachOpen" x-cloak
        class="fixed inset-0 z-50 flex items-end justify-center"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
    >
        <div class="absolute inset-0 bg-black/40" wire:click="cancelAttach"></div>
        <div class="relative w-full max-w-lg rounded-t-2xl bg-white p-6 space-y-4 safe-bottom"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0"
        >
            <div class="mx-auto h-1 w-10 rounded-full bg-gray-200 -mt-2 mb-2"></div>
            <h3 class="text-base font-semibold text-ink">Attach result files</h3>
            <p class="text-sm text-ink-muted">PDF, JPG, PNG, TIFF up to 20MB each, max 5 files.</p>

            <input
                wire:model="resultFiles"
                type="file"
                multiple
                accept=".pdf,.jpg,.jpeg,.png,.tiff,.tif"
                class="block w-full text-sm text-ink-muted file:mr-3 file:rounded-lg file:border-0 file:bg-primary/10 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-primary hover:file:bg-primary/20"
            >
            @error('resultFiles') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            @error('resultFiles.*') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror

            <div class="flex gap-3">
                <button wire:click="saveAttachments"
                    class="flex-1 rounded-xl bg-primary py-3 text-sm font-semibold text-white min-h-12 transition active:scale-[0.98]">
                    <span wire:loading.remove wire:target="saveAttachments">Upload</span>
                    <span wire:loading wire:target="saveAttachments">Uploading…</span>
                </button>
                <button wire:click="cancelAttach"
                    class="rounded-xl border border-gray-300 px-5 py-3 text-sm font-medium text-ink-muted min-h-12">
                    Cancel
                </button>
            </div>
        </div>
    </div>

</div>
