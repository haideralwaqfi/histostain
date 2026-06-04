{{-- Recut --}}
<div class="space-y-4">
    @foreach($typeData['blocks'] ?? [] as $i => $block)
        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-sm font-semibold text-ink">Block {{ $i + 1 }}</span>
                @if(count($typeData['blocks']) > 1)
                    <button type="button" wire:click="removeBlock({{ $i }})" class="text-xs text-red-500">Remove</button>
                @endif
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div><x-form.input wire:model="typeData.blocks.{{ $i }}.block_id" label="Block ID" required /></div>
                <div><x-form.input wire:model="typeData.blocks.{{ $i }}.thickness" type="number" label="Thickness (µm)" min="1" max="20" step="0.5" /></div>
                <div class="col-span-2"><x-form.input wire:model="typeData.blocks.{{ $i }}.levels" label="Levels" placeholder="e.g. 3 levels, 50µm apart" required /></div>
                <div class="col-span-2"><x-form.textarea wire:model="typeData.blocks.{{ $i }}.reason" label="Reason for recut" rows="2" required /></div>
                <div class="col-span-2">
                    <label class="flex items-center gap-2 text-sm text-ink cursor-pointer">
                        <input wire:model.live="typeData.blocks.{{ $i }}.restain_after" type="checkbox" class="rounded border-gray-300 text-primary">
                        Restain after recut
                    </label>
                </div>
                @if($typeData['blocks'][$i]['restain_after'] ?? false)
                    <div class="col-span-2"><x-form.input wire:model="typeData.blocks.{{ $i }}.restain_stain" label="Restain with" placeholder="e.g. H&E" required /></div>
                @endif
            </div>
        </div>
    @endforeach
    <button type="button" wire:click="addBlock" class="flex w-full items-center justify-center gap-2 rounded-xl border-2 border-dashed border-gray-300 py-3 text-sm font-medium text-ink-muted hover:border-primary hover:text-primary transition min-h-[48px]">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Add another block
    </button>
</div>
