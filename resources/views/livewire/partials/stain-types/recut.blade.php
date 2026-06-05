{{-- Recut --}}
<div class="space-y-4">
    @foreach($typeData['blocks'] ?? [] as $i => $block)
        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 space-y-3"
            x-data="{
                restain: @entangle($prefix . '.blocks.' . $i . '.restain_after')
            }">

            <div class="flex items-center justify-between">
                <span class="text-sm font-semibold text-ink">Block {{ $i + 1 }}</span>
                @if(count($typeData['blocks']) > 1)
                    <button type="button" wire:click="removeBlock('{{ $typeKey }}', {{ $i }})" wire:loading.class="opacity-50 pointer-events-none" class="text-xs text-red-500">Remove</button>
                @endif
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <x-form.input wire:model="{{ $prefix }}.blocks.{{ $i }}.block_id" label="Block ID" required />
                    @error($prefix . '.blocks.' . $i . '.block_id') <x-form.error>{{ $message }}</x-form.error> @enderror
                </div>
                <div>
                    <x-form.input wire:model="{{ $prefix }}.blocks.{{ $i }}.thickness" type="number" label="Thickness (µm)" min="1" max="20" step="0.5" />
                </div>
                <div class="col-span-2">
                    <x-form.input wire:model="{{ $prefix }}.blocks.{{ $i }}.levels" label="Levels" placeholder="e.g. 3 levels, 50µm apart" required />
                    @error($prefix . '.blocks.' . $i . '.levels') <x-form.error>{{ $message }}</x-form.error> @enderror
                </div>
                <div class="col-span-2">
                    <x-form.textarea wire:model="{{ $prefix }}.blocks.{{ $i }}.reason" label="Reason for recut" rows="2" required />
                    @error($prefix . '.blocks.' . $i . '.reason') <x-form.error>{{ $message }}</x-form.error> @enderror
                </div>
                <div class="col-span-2">
                    <label class="flex items-center gap-2 text-sm text-ink cursor-pointer">
                        <input
                            wire:model="{{ $prefix }}.blocks.{{ $i }}.restain_after"
                            x-model="restain"
                            type="checkbox"
                            class="rounded border-gray-300 text-primary"
                        >
                        Restain after recut
                    </label>
                </div>
                {{-- Shown instantly by Alpine — no server round-trip --}}
                <div class="col-span-2" x-show="restain" x-cloak>
                    <x-form.input wire:model="{{ $prefix }}.blocks.{{ $i }}.restain_stain" label="Restain with" placeholder="e.g. H&E" />
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
