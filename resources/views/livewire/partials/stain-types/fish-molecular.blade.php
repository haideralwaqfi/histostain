{{-- FISH / Molecular --}}
<div class="space-y-4">
    @foreach($typeData['blocks'] ?? [] as $i => $block)
        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 space-y-3">
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
                    <x-form.input wire:model="{{ $prefix }}.blocks.{{ $i }}.probe" label="Probe" />
                    @error($prefix . '.blocks.' . $i . '.probe') <x-form.error>{{ $message }}</x-form.error> @enderror
                </div>
                <div class="col-span-2">
                    <x-form.textarea wire:model="{{ $prefix }}.blocks.{{ $i }}.indication" label="Indication" rows="2" />
                    @error($prefix . '.blocks.' . $i . '.indication') <x-form.error>{{ $message }}</x-form.error> @enderror
                </div>
                <div class="col-span-2">
                    <x-form.input wire:model="{{ $prefix }}.blocks.{{ $i }}.send_out_lab" label="Send-out lab (optional)" />
                </div>
                <div class="col-span-2">
                    <x-form.textarea wire:model="{{ $prefix }}.blocks.{{ $i }}.special_instructions" label="Special instructions" rows="2" />
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
