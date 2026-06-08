{{-- Special Stain --}}
<div class="space-y-4">
    @foreach($typeData['blocks'] ?? [] as $i => $block)
        {{-- Alpine owns visual state; @entangle pushes to Livewire on next request (no live round-trip) --}}
        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 space-y-3"
            x-data="{
                selected: @entangle($prefix . '.blocks.' . $i . '.stains')
            }">

            <div class="flex items-center justify-between">
                <span class="text-sm font-semibold text-ink">Block {{ $i + 1 }}</span>
                @if(count($typeData['blocks']) > 1)
                    <button type="button" wire:click="removeBlock('{{ $typeKey }}', {{ $i }})" wire:loading.class="opacity-50 pointer-events-none" class="text-xs text-red-500">Remove</button>
                @endif
            </div>

            <div class="space-y-3">
                <div>
                    <x-form.input wire:model="{{ $prefix }}.blocks.{{ $i }}.block_id" label="Block ID" required />
                    @error($prefix . '.blocks.' . $i . '.block_id') <x-form.error>{{ $message }}</x-form.error> @enderror
                </div>

                {{-- Pill checkboxes for stains --}}
                <div>
                    <p class="block text-sm font-medium text-ink mb-2">
                        Stain(s) <span class="text-red-500">*</span>
                    </p>
                    <div class="flex flex-wrap gap-2">
                        @foreach(\App\StainTypes\Types\SpecialStainType::allOptionsWithMeta() as $val => $meta)
                            @if($meta['is_active'])
                                {{-- Active: normal selectable pill --}}
                                <label
                                    :class="selected.includes('{{ $val }}')
                                        ? 'border-primary bg-primary/10 text-primary'
                                        : 'border-gray-200 bg-white text-ink-muted hover:border-gray-300'"
                                    class="flex cursor-pointer items-center rounded-full border-2 px-3 py-1 text-xs font-semibold transition select-none">
                                    <input
                                        x-model="selected"
                                        type="checkbox"
                                        value="{{ $val }}"
                                        class="sr-only"
                                    >
                                    {{ $meta['label'] }}
                                </label>
                            @else
                                {{-- Inactive: red pill with reason popup on tap --}}
                                <div class="relative" x-data="{ open: false }">
                                    <button
                                        type="button"
                                        @click="open = !open"
                                        class="flex items-center gap-1 rounded-full border-2 border-red-200 bg-red-50 px-3 py-1 text-xs font-semibold text-red-400 cursor-not-allowed select-none">
                                        {{ $meta['label'] }}
                                        <svg class="h-3 w-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                                        </svg>
                                    </button>
                                    <div
                                        x-show="open"
                                        x-cloak
                                        @click.outside="open = false"
                                        class="absolute bottom-full left-0 mb-2 z-50 w-56 rounded-xl border border-red-100 bg-white shadow-xl p-3">
                                        <p class="text-xs font-semibold text-red-600 mb-1">Currently Unavailable</p>
                                        <p class="text-xs text-gray-500 leading-relaxed">
                                            {{ $meta['inactive_reason'] ?: 'This stain is not currently available.' }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    @error($prefix . '.blocks.' . $i . '.stains') <x-form.error>{{ $message }}</x-form.error> @enderror
                </div>

                {{-- "Other" text field — shown instantly by Alpine, no server needed --}}
                <div x-show="selected.includes('other')" x-cloak>
                    <x-form.input wire:model="{{ $prefix }}.blocks.{{ $i }}.stain_other" label="Specify other stain" />
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <x-form.input wire:model="{{ $prefix }}.blocks.{{ $i }}.section_count" type="number" label="Sections" min="1" required />
                    </div>
                    <div class="col-span-2">
                        <x-form.textarea wire:model="{{ $prefix }}.blocks.{{ $i }}.indication" label="Indication" rows="2" />
                        @error($prefix . '.blocks.' . $i . '.indication') <x-form.error>{{ $message }}</x-form.error> @enderror
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <button type="button" wire:click="addBlock('{{ $typeKey }}')"
        wire:loading.class="opacity-50 pointer-events-none"
        class="flex w-full items-center justify-center gap-2 rounded-xl border-2 border-dashed border-gray-300 py-3 text-sm font-medium text-ink-muted hover:border-primary hover:text-primary transition min-h-12">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Add another block
    </button>
</div>
