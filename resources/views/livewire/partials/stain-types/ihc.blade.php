{{-- IHC / Immunohistochemistry --}}
<div class="space-y-4">
    @foreach($typeData['blocks'] ?? [] as $i => $block)
        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-sm font-semibold text-ink">Block {{ $i + 1 }}</span>
                @if(count($typeData['blocks']) > 1)
                    <button type="button" wire:click="removeBlock('{{ $typeKey }}', {{ $i }})"
                        wire:loading.class="opacity-50 pointer-events-none"
                        class="text-xs font-medium text-red-500 hover:text-red-700">Remove</button>
                @endif
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="col-span-2">
                    <x-form.input wire:model="{{ $prefix }}.blocks.{{ $i }}.block_id" label="Block ID" placeholder="e.g. A1" required />
                    @error($prefix . '.blocks.' . $i . '.block_id') <x-form.error>{{ $message }}</x-form.error> @enderror
                </div>
                <div>
                    <x-form.input wire:model="{{ $prefix }}.blocks.{{ $i }}.antibody" label="Antibody" placeholder="e.g. CD20" required />
                    @error($prefix . '.blocks.' . $i . '.antibody') <x-form.error>{{ $message }}</x-form.error> @enderror
                </div>
                <div>
                    <x-form.input wire:model="{{ $prefix }}.blocks.{{ $i }}.clone" label="Clone" placeholder="e.g. L26" />
                </div>
                <div>
                    <x-form.input wire:model="{{ $prefix }}.blocks.{{ $i }}.dilution" label="Dilution" placeholder="e.g. 1:100" />
                </div>
                <div>
                    <x-form.input wire:model="{{ $prefix }}.blocks.{{ $i }}.section_count" type="number" label="Sections" min="1" max="50" required />
                    @error($prefix . '.blocks.' . $i . '.section_count') <x-form.error>{{ $message }}</x-form.error> @enderror
                </div>
                <div class="col-span-2">
                    <x-form.textarea wire:model="{{ $prefix }}.blocks.{{ $i }}.clinical_indication" label="Clinical indication" rows="2" />
                    @error($prefix . '.blocks.' . $i . '.clinical_indication') <x-form.error>{{ $message }}</x-form.error> @enderror
                </div>
                <div class="col-span-2">
                    <label class="flex items-center gap-2 text-sm text-ink cursor-pointer">
                        <input wire:model="{{ $prefix }}.blocks.{{ $i }}.controls_required" type="checkbox"
                            class="rounded border-gray-300 text-primary focus:ring-primary">
                        Controls required
                    </label>
                </div>
            </div>
        </div>
    @endforeach

    <button type="button" wire:click="addBlock('{{ $typeKey }}')"
        wire:loading.class="opacity-50 pointer-events-none"
        class="flex w-full items-center justify-center gap-2 rounded-xl border-2 border-dashed border-gray-300 py-3 text-sm font-medium text-ink-muted hover:border-primary hover:text-primary transition-colors min-h-12">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Add another block
    </button>
</div>
