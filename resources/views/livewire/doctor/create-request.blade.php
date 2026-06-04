<div class="safe-top">
    {{-- Step indicator --}}
    <div class="sticky top-0 z-10 bg-white border-b border-gray-200">
        <div class="flex items-center gap-1 px-4 py-3">
            @foreach(['type' => 'Type', 'details' => 'Details', 'review' => 'Review'] as $s => $label)
                <div class="flex items-center gap-1 @if(!$loop->first) flex-1 @endif">
                    @if(!$loop->first)
                        <div class="h-px flex-1 {{ in_array($step, array_slice(array_keys(['type','details','review']), $loop->index)) ? 'bg-primary' : 'bg-gray-200' }}"></div>
                    @endif
                    <div class="flex items-center gap-1.5">
                        <div class="flex h-6 w-6 items-center justify-center rounded-full text-xs font-semibold
                            {{ $step === $s ? 'bg-primary text-white' : (array_search($step, ['type','details','review']) > array_search($s, ['type','details','review']) ? 'bg-green-500 text-white' : 'bg-gray-200 text-ink-muted') }}">
                            {{ $loop->iteration }}
                        </div>
                        <span class="text-xs font-medium {{ $step === $s ? 'text-primary' : 'text-ink-muted' }} hidden sm:inline">{{ $label }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="px-4 py-5">

        {{-- ── Step 1: Type selector ── --}}
        @if($step === 'type')
            <h2 class="text-lg font-semibold text-ink mb-4">Select stain type</h2>
            <div class="space-y-2">
                @foreach($typeOptions as $value => $label)
                    <button
                        type="button"
                        wire:click="$set('type', '{{ $value }}')"
                        class="w-full flex items-center justify-between rounded-xl border-2 px-4 py-3.5 text-left text-sm font-medium transition min-h-[52px]
                            {{ $type === $value ? 'border-primary bg-primary/5 text-primary' : 'border-gray-200 bg-white text-ink hover:border-gray-300' }}"
                    >
                        {{ $label }}
                        @if($type === $value)
                            <svg class="h-5 w-5 text-primary flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        @endif
                    </button>
                @endforeach
            </div>
            @error('type') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
            <div class="mt-6">
                <button wire:click="goToDetails" class="w-full rounded-xl bg-primary py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark active:scale-[0.98] min-h-[52px] disabled:opacity-50" @if(!$type) disabled @endif>
                    Continue
                </button>
            </div>
        @endif

        {{-- ── Step 2: Shared + type fields ── --}}
        @if($step === 'details')
            <div class="flex items-center gap-3 mb-5">
                <button wire:click="backToType" class="text-sm text-ink-muted hover:text-ink">← Back</button>
                <h2 class="text-lg font-semibold text-ink">{{ $typeDefinition?->label() }}</h2>
            </div>

            <form wire:submit="goToReview" class="space-y-5" novalidate>

                {{-- Shared clinical fields --}}
                <div class="rounded-xl border border-gray-200 bg-white p-4 space-y-3">
                    <h3 class="text-sm font-semibold text-ink-muted uppercase tracking-wide">Patient / Case</h3>
                    <x-form.input wire:model="caseNumber" label="Case number" required placeholder="e.g. 2024-12345" />
                    @error('caseNumber') <x-form.error>{{ $message }}</x-form.error> @enderror
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <x-form.input wire:model="mrn" label="MRN" placeholder="Optional" />
                        </div>
                        <div>
                            <x-form.input wire:model="labNumber" label="Lab number" placeholder="Optional" />
                        </div>
                    </div>
                </div>

                {{-- Priority --}}
                <div class="rounded-xl border border-gray-200 bg-white p-4">
                    <h3 class="text-sm font-semibold text-ink-muted uppercase tracking-wide mb-3">Priority</h3>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach($priorities as $p)
                            <button
                                type="button"
                                wire:click="$set('priority', '{{ $p->value }}')"
                                class="rounded-xl border-2 py-2.5 text-sm font-semibold transition min-h-[44px]
                                    @if($priority === $p->value)
                                        @if($p->value === 'stat') border-red-500 bg-red-50 text-red-700
                                        @elseif($p->value === 'urgent') border-amber-400 bg-amber-50 text-amber-700
                                        @else border-primary bg-primary/5 text-primary @endif
                                    @else border-gray-200 text-ink-muted hover:border-gray-300 @endif"
                            >
                                {{ $p->label() }}
                            </button>
                        @endforeach
                    </div>
                    @error('priority') <x-form.error class="mt-1">{{ $message }}</x-form.error> @enderror
                </div>

                {{-- Type-specific fields --}}
                <div class="rounded-xl border border-gray-200 bg-white p-4">
                    <h3 class="text-sm font-semibold text-ink-muted uppercase tracking-wide mb-3">
                        {{ $typeDefinition?->label() }} Details
                    </h3>
                    @if($type)
                        @include($typeDefinition->formPartial())
                    @endif
                </div>

                {{-- Notes --}}
                <div class="rounded-xl border border-gray-200 bg-white p-4">
                    <x-form.textarea wire:model="notes" label="Additional notes" rows="3" placeholder="Optional clinical notes…" />
                </div>

                {{-- Attachments --}}
                <div class="rounded-xl border border-gray-200 bg-white p-4">
                    <h3 class="text-sm font-semibold text-ink-muted uppercase tracking-wide mb-2">Attachments (optional)</h3>
                    <p class="text-xs text-ink-muted mb-2">Requisition forms, images — PDF, JPG, PNG up to 10MB each, max 5 files.</p>
                    <input
                        wire:model="attachments"
                        type="file"
                        multiple
                        accept=".pdf,.jpg,.jpeg,.png"
                        class="block w-full text-sm text-ink-muted file:mr-3 file:rounded-lg file:border-0 file:bg-primary/10 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-primary hover:file:bg-primary/20"
                    >
                    @error('attachments.*') <x-form.error>{{ $message }}</x-form.error> @enderror
                </div>

                <button type="submit" class="w-full rounded-xl bg-primary py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark active:scale-[0.98] min-h-[52px]">
                    <span wire:loading.remove wire:target="goToReview">Review request</span>
                    <span wire:loading wire:target="goToReview">Validating…</span>
                </button>
            </form>
        @endif

        {{-- ── Step 3: Review ── --}}
        @if($step === 'review')
            <div class="flex items-center gap-3 mb-5">
                <button wire:click="backToDetails" class="text-sm text-ink-muted hover:text-ink">← Back</button>
                <h2 class="text-lg font-semibold text-ink">Review & submit</h2>
            </div>

            {{-- Summary card --}}
            <div class="rounded-2xl border border-gray-200 bg-white divide-y divide-gray-100 mb-5">
                <div class="px-4 py-3 flex justify-between text-sm">
                    <span class="text-ink-muted">Type</span>
                    <span class="font-medium text-ink">{{ $typeDefinition?->label() }}</span>
                </div>
                <div class="px-4 py-3 flex justify-between text-sm">
                    <span class="text-ink-muted">Case number</span>
                    <span class="font-medium text-ink">{{ $caseNumber }}</span>
                </div>
                @if($mrn)
                    <div class="px-4 py-3 flex justify-between text-sm">
                        <span class="text-ink-muted">MRN</span>
                        <span class="font-medium text-ink">{{ $mrn }}</span>
                    </div>
                @endif
                @if($labNumber)
                    <div class="px-4 py-3 flex justify-between text-sm">
                        <span class="text-ink-muted">Lab number</span>
                        <span class="font-medium text-ink">{{ $labNumber }}</span>
                    </div>
                @endif
                <div class="px-4 py-3 flex justify-between text-sm">
                    <span class="text-ink-muted">Priority</span>
                    <span class="font-semibold
                        {{ $priority === 'stat' ? 'text-red-600' : ($priority === 'urgent' ? 'text-amber-600' : 'text-ink') }}">
                        {{ strtoupper($priority) }}
                    </span>
                </div>
                @if($notes)
                    <div class="px-4 py-3 text-sm">
                        <span class="text-ink-muted block mb-1">Notes</span>
                        <span class="text-ink">{{ $notes }}</span>
                    </div>
                @endif
                @if(!empty($typeData['blocks']))
                    <div class="px-4 py-3 text-sm">
                        <span class="text-ink-muted block mb-1">Blocks</span>
                        <span class="font-medium text-ink">{{ count($typeData['blocks']) }} block(s)</span>
                    </div>
                @endif
                @if(!empty($attachments))
                    <div class="px-4 py-3 text-sm">
                        <span class="text-ink-muted">Attachments</span>
                        <span class="font-medium text-ink ml-2">{{ count($attachments) }} file(s)</span>
                    </div>
                @endif
            </div>

            @if($priority === 'stat')
                <div class="rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700 mb-4 flex items-center gap-2">
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                    STAT request — all available techs will be notified immediately.
                </div>
            @endif

            <button
                wire:click="submit"
                wire:loading.attr="disabled"
                class="w-full rounded-xl bg-primary py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark active:scale-[0.98] min-h-[52px] disabled:opacity-60"
            >
                <span wire:loading.remove wire:target="submit">Submit request</span>
                <span wire:loading wire:target="submit">Submitting…</span>
            </button>
        @endif

    </div>
</div>
