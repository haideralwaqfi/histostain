{{-- Cytology Special Stain — single slide, not block array --}}
<div class="space-y-3">
    <x-form.input wire:model="typeData.slide_id" label="Slide ID" required />
    @error('typeData.slide_id') <x-form.error>{{ $message }}</x-form.error> @enderror

    <x-form.select wire:model.live="typeData.stain" label="Stain" required>
        <option value="">Select stain…</option>
        @foreach(\App\StainTypes\Types\CytologySpecialStainType::STAIN_OPTIONS as $val => $lbl)
            <option value="{{ $val }}">{{ $lbl }}</option>
        @endforeach
    </x-form.select>
    @error('typeData.stain') <x-form.error>{{ $message }}</x-form.error> @enderror

    @if(($typeData['stain'] ?? '') === 'other')
        <x-form.input wire:model="typeData.stain_other" label="Specify stain" required />
    @endif

    <x-form.textarea wire:model="typeData.indication" label="Indication" rows="2" required />
    @error('typeData.indication') <x-form.error>{{ $message }}</x-form.error> @enderror
</div>
