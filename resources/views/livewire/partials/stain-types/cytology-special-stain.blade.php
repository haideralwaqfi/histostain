{{-- Cytology Special Stain — single slide, not block array --}}
<div class="space-y-3"
    x-data="{
        stain: @entangle($prefix . '.stain')
    }">

    <x-form.input wire:model="{{ $prefix }}.slide_id" label="Slide ID" required />
    @error($prefix . '.slide_id') <x-form.error>{{ $message }}</x-form.error> @enderror

    <div>
        <label class="block text-sm font-medium text-ink mb-1">Stain <span class="text-red-500">*</span></label>
        <select
            wire:model="{{ $prefix }}.stain"
            x-model="stain"
            class="block w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
        >
            <option value="">Select stain…</option>
            @foreach(\App\StainTypes\Types\CytologySpecialStainType::STAIN_OPTIONS as $val => $lbl)
                <option value="{{ $val }}">{{ $lbl }}</option>
            @endforeach
        </select>
        @error($prefix . '.stain') <x-form.error>{{ $message }}</x-form.error> @enderror
    </div>

    {{-- Shown instantly by Alpine — no server round-trip --}}
    <div x-show="stain === 'other'" x-cloak>
        <x-form.input wire:model="{{ $prefix }}.stain_other" label="Specify stain" required />
    </div>

    <x-form.textarea wire:model="{{ $prefix }}.indication" label="Indication" rows="2" />
    @error($prefix . '.indication') <x-form.error>{{ $message }}</x-form.error> @enderror
</div>
