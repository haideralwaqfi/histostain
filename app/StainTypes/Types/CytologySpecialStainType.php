<?php

namespace App\StainTypes\Types;

use App\StainTypes\Contracts\StainTypeDefinition;

class CytologySpecialStainType implements StainTypeDefinition
{
    public const STAIN_OPTIONS = [
        'pap' => 'Pap',
        'h_and_e' => 'H&E',
        'diff_quik' => 'Diff-Quik',
        'mucicarmine' => 'Mucicarmine',
        'pas' => 'PAS',
        'cell_block' => 'Cell Block',
        'other' => 'Other',
    ];

    public function label(): string { return 'Cytology Special Stain'; }

    // Cytology uses slides, not blocks — the array structure still applies but
    // the field is labelled "Slide ID" in the UI.
    public function supportsMultipleBlocks(): bool { return false; }

    public function formPartial(): string { return 'livewire.partials.stain-types.cytology-special-stain'; }

    public function defaultData(): array
    {
        return [
            'slide_id' => '',
            'stain' => '',
            'stain_other' => '',
            'indication' => '',
        ];
    }

    public function rules(): array
    {
        return [
            'typeData.slide_id' => 'required|string|max:100',
            'typeData.stain' => 'required|string|in:' . implode(',', array_keys(self::STAIN_OPTIONS)),
            'typeData.stain_other' => 'nullable|string|max:200',
            'typeData.indication' => 'nullable|string|max:500',
        ];
    }
}
