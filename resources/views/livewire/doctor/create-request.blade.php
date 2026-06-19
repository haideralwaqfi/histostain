<div class="safe-top">

    {{-- Step indicator --}}
    <div class="sticky top-0 z-10 bg-white border-b border-gray-200">
        <div class="flex items-center gap-1 px-4 py-3">
            @foreach(['type' => 'Types', 'details' => 'Details', 'review' => 'Review'] as $s => $label)
                @php $steps = ['type','details','review']; @endphp
                <div class="flex items-center gap-1 @if(!$loop->first) flex-1 @endif">
                    @if(!$loop->first)
                        <div class="h-px flex-1 {{ array_search($step, $steps) >= array_search($s, $steps) ? 'bg-primary' : 'bg-gray-200' }}"></div>
                    @endif
                    <div class="flex items-center gap-1.5">
                        <div class="flex h-6 w-6 items-center justify-center rounded-full text-xs font-semibold
                            {{ $step === $s ? 'bg-primary text-white' : (array_search($step, $steps) > array_search($s, $steps) ? 'bg-green-500 text-white' : 'bg-gray-200 text-ink-muted') }}">
                            {{ $loop->iteration }}
                        </div>
                        <span class="text-xs font-medium {{ $step === $s ? 'text-primary' : 'text-ink-muted' }} hidden sm:inline">{{ $label }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="px-4 py-5">

        {{-- ────────────────────────────────────────────────────────
             Step 1: Multi-select stain types
        ─────────────────────────────────────────────────────────── --}}
        @if($step === 'type')
            <h2 class="text-lg font-semibold text-ink mb-1">Select stain types</h2>
            <p class="text-sm text-ink-muted mb-4">Choose one or more — all will be submitted as separate requests sharing the same case.</p>

            <div class="space-y-2">
                @foreach($typeOptions as $value => $label)
                    @php $selected = in_array($value, $selectedTypes); @endphp
                    <button
                        type="button"
                        wire:click="toggleType('{{ $value }}')"
                        wire:loading.attr="disabled" wire:target="toggleType('{{ $value }}')"
                        class="w-full flex items-center justify-between rounded-xl border-2 px-4 py-3.5 text-left text-sm font-medium transition min-h-[52px] disabled:opacity-60
                            {{ $selected ? 'border-primary bg-primary/5 text-primary' : 'border-gray-200 bg-white text-ink hover:border-gray-300' }}"
                    >
                        {{ $label }}
                        <div class="ml-3 shrink-0">
                            {{-- Spinner replaces checkbox while the request is in flight --}}
                            <x-spinner wire:loading wire:target="toggleType('{{ $value }}')" class="h-4 w-4 text-primary" />

                            {{-- Checkbox shown when idle --}}
                            <div wire:loading.remove wire:target="toggleType('{{ $value }}')"
                                class="flex h-5 w-5 items-center justify-center rounded border-2 transition
                                    {{ $selected ? 'border-primary bg-primary' : 'border-gray-300' }}">
                                @if($selected)
                                    <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                    </svg>
                                @endif
                            </div>
                        </div>
                    </button>
                @endforeach
            </div>

            @error('selectedTypes')
                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
            @enderror

            <div class="mt-6">
                @if(count($selectedTypes) > 0)
                    <p class="text-center text-sm text-ink-muted mb-3">
                        {{ count($selectedTypes) }} {{ count($selectedTypes) === 1 ? 'type' : 'types' }} selected
                    </p>
                @endif
                <button
                    wire:click="goToDetails"
                    wire:loading.attr="disabled" wire:target="goToDetails"
                    class="w-full rounded-xl bg-primary py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark active:scale-[0.98] min-h-[52px] disabled:opacity-40"
                    @if(count($selectedTypes) === 0) disabled @endif
                >
                    <span wire:loading.remove wire:target="goToDetails">Continue</span>
                    <span wire:loading wire:target="goToDetails" class="inline-flex items-center justify-center gap-2">
                        <x-spinner /> Loading…
                    </span>
                </button>
            </div>
        @endif

        {{-- ────────────────────────────────────────────────────────
             Step 2: Shared fields + per-type details
        ─────────────────────────────────────────────────────────── --}}
        @if($step === 'details')
            <div class="flex items-center gap-3 mb-5">
                <button wire:click="backToType" class="text-sm text-ink-muted hover:text-ink">← Back</button>
                <h2 class="text-lg font-semibold text-ink">Request details</h2>
            </div>

            <form wire:submit="goToReview" class="space-y-4" novalidate>

                {{-- Patient / Case --}}
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
                                wire:loading.class="opacity-50 pointer-events-none"
                                class="rounded-xl border-2 py-2.5 text-sm font-semibold transition min-h-11
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

                {{-- Per-type forms — tabbed if multiple types --}}
                @php $typeCount = count($selectedTypes); @endphp

                @if($typeCount > 1)
                    <div x-data="{ tab: '{{ $selectedTypes[0] }}' }">

                        {{-- Tab bar --}}
                        <div class="flex gap-2 overflow-x-auto pb-1 -mx-1 px-1">
                            @foreach($selectedTypes as $typeKey)
                                <button
                                    type="button"
                                    @click="tab = '{{ $typeKey }}'"
                                    :class="tab === '{{ $typeKey }}' ? 'bg-primary text-white' : 'bg-gray-100 text-ink-muted hover:bg-gray-200'"
                                    class="shrink-0 rounded-full px-3 py-1.5 text-xs font-semibold transition"
                                >
                                    {{ \App\Enums\StainRequestType::from($typeKey)->shortLabel() }}
                                </button>
                            @endforeach
                        </div>

                        {{-- One form section per type --}}
                        @foreach($selectedTypes as $typeKey)
                            @php
                                $definition = \App\StainTypes\StainTypeRegistry::get($typeKey);
                                $typeData   = $typesData[$typeKey] ?? [];
                                $prefix     = 'typesData.' . $typeKey;
                            @endphp
                            <div x-show="tab === '{{ $typeKey }}'"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                class="mt-3">
                                <div class="rounded-xl border border-gray-200 bg-white p-4">
                                    <h3 class="text-sm font-semibold text-ink-muted uppercase tracking-wide mb-3">
                                        {{ $definition->label() }}
                                    </h3>
                                    @include($definition->formPartial(), [
                                        'typeData' => $typeData,
                                        'prefix'   => $prefix,
                                        'typeKey'  => $typeKey,
                                    ])
                                </div>
                            </div>
                        @endforeach
                    </div>

                @else
                    {{-- Single type — no tab chrome --}}
                    @php
                        $typeKey    = $selectedTypes[0];
                        $definition = \App\StainTypes\StainTypeRegistry::get($typeKey);
                        $typeData   = $typesData[$typeKey] ?? [];
                        $prefix     = 'typesData.' . $typeKey;
                    @endphp
                    <div class="rounded-xl border border-gray-200 bg-white p-4">
                        <h3 class="text-sm font-semibold text-ink-muted uppercase tracking-wide mb-3">
                            {{ $definition->label() }}
                        </h3>
                        @include($definition->formPartial(), [
                            'typeData' => $typeData,
                            'prefix'   => $prefix,
                            'typeKey'  => $typeKey,
                        ])
                    </div>
                @endif

                {{-- Notes --}}
                <div class="rounded-xl border border-gray-200 bg-white p-4">
                    <x-form.textarea wire:model="notes" label="Additional notes" rows="3" placeholder="Optional clinical notes…" />
                </div>

                {{-- Attachments --}}
                <div class="rounded-xl border border-gray-200 bg-white p-4">
                    <h3 class="text-sm font-semibold text-ink-muted uppercase tracking-wide mb-2">Attachments (optional)</h3>
                    <p class="text-xs text-ink-muted mb-2">
                        Requisition forms, images — PDF, JPG, PNG up to 10 MB each, max 5 files.
                        @if($typeCount > 1) Files will be attached to all {{ $typeCount }} requests. @endif
                    </p>
                    <input
                        wire:model="attachments"
                        type="file"
                        multiple
                        accept=".pdf,.jpg,.jpeg,.png"
                        class="block w-full text-sm text-ink-muted file:mr-3 file:rounded-lg file:border-0 file:bg-primary/10 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-primary hover:file:bg-primary/20"
                    >
                    @error('attachments.*') <x-form.error>{{ $message }}</x-form.error> @enderror
                </div>

                <button type="submit"
                    wire:loading.attr="disabled" wire:target="goToReview"
                    class="w-full rounded-xl bg-primary py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark active:scale-[0.98] min-h-[52px] disabled:opacity-60">
                    <span wire:loading.remove wire:target="goToReview">Review request</span>
                    <span wire:loading wire:target="goToReview" class="inline-flex items-center justify-center gap-2">
                        <x-spinner /> Validating…
                    </span>
                </button>
            </form>
        @endif

        {{-- ────────────────────────────────────────────────────────
             Step 3: Review & submit
        ─────────────────────────────────────────────────────────── --}}
        @if($step === 'review')
            <div class="flex items-center gap-3 mb-5">
                <button wire:click="backToDetails" class="text-sm text-ink-muted hover:text-ink">← Back</button>
                <h2 class="text-lg font-semibold text-ink">Review & submit</h2>
            </div>

            @php $typeCount = count($selectedTypes); @endphp

            @if($typeCount > 1)
                <p class="text-sm text-ink-muted mb-4">
                    <span class="font-semibold text-ink">{{ $typeCount }} requests</span> will be created — one per stain type, sharing the same case and priority.
                </p>
            @endif

            {{-- Shared info --}}
            <div class="rounded-2xl border border-gray-200 bg-white divide-y divide-gray-100 mb-4">
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
                    <span class="font-semibold {{ $priority === 'stat' ? 'text-red-600' : ($priority === 'urgent' ? 'text-amber-600' : 'text-ink') }}">
                        {{ strtoupper($priority) }}
                    </span>
                </div>
                @if($notes)
                    <div class="px-4 py-3 text-sm">
                        <span class="text-ink-muted block mb-1">Notes</span>
                        <span class="text-ink">{{ $notes }}</span>
                    </div>
                @endif
                @if(!empty($attachments))
                    <div class="px-4 py-3 flex justify-between text-sm">
                        <span class="text-ink-muted">Attachments</span>
                        <span class="font-medium text-ink">{{ count($attachments) }} file(s)</span>
                    </div>
                @endif
            </div>

            {{-- Per-type summary cards --}}
            <div class="space-y-2 mb-5">
                @foreach($selectedTypes as $typeKey)
                    @php
                        $typeEnum   = \App\Enums\StainRequestType::from($typeKey);
                        $typeData   = $typesData[$typeKey] ?? [];
                        $blockCount = isset($typeData['blocks']) ? count($typeData['blocks']) : null;
                    @endphp
                    <div class="rounded-xl border border-gray-200 bg-white px-4 py-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-ink">{{ $typeEnum->label() }}</p>
                            @if($blockCount !== null)
                                <p class="text-xs text-ink-muted">{{ $blockCount }} {{ $typeEnum === \App\Enums\StainRequestType::CytologySpecialStain ? 'slide' : 'block' }}(s)</p>
                            @else
                                <p class="text-xs text-ink-muted">1 slide</p>
                            @endif
                        </div>
                        <x-status-badge :status="\App\Enums\StainRequestStatus::Pending" />
                    </div>
                @endforeach
            </div>

            @if($priority === 'stat')
                <div class="rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700 mb-4 flex items-center gap-2">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                    STAT request — all available techs will be notified immediately.
                </div>
            @endif

            <button
                wire:click="submit"
                wire:loading.attr="disabled" wire:target="submit"
                class="w-full rounded-xl bg-primary py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-dark active:scale-[0.98] min-h-[52px] disabled:opacity-60"
            >
                <span wire:loading.remove wire:target="submit">
                    Submit {{ $typeCount > 1 ? $typeCount . ' requests' : 'request' }}
                </span>
                <span wire:loading wire:target="submit" class="inline-flex items-center justify-center gap-2">
                    <x-spinner /> Submitting…
                </span>
            </button>
        @endif

    </div>
</div>
