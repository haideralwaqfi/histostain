<?php

namespace App\StainTypes\Types;

use App\StainTypes\Contracts\StainTypeDefinition;

class SpecialStainType implements StainTypeDefinition
{
    public const STAIN_OPTIONS = [
        'pas' => 'PAS',
        'masson_trichrome' => 'Masson Trichrome',
        'ziehl_neelsen' => 'Ziehl-Neelsen',
        'reticulin' => 'Reticulin',
        'congo_red' => 'Congo Red',
        'alcian_blue' => 'Alcian Blue',
        'giemsa' => 'Giemsa',
        'grocott' => 'Grocott',
        'ptah' => 'PTAH',
        'other' => 'Other',
    ];

    public function label(): string { return 'Special Stain'; }

    public function supportsMultipleBlocks(): bool { return true; }

    public function formPartial(): string { return 'livewire.partials.stain-types.special-stain'; }

    public function defaultData(): array
    {
        return [
            'blocks' => [
                [
                    'block_id'    => '',
                    'stains'      => [],
                    'stain_other' => '',
                    'indication'  => '',
                    'section_count' => 1,
                ],
            ],
        ];
    }

    public function rules(): array
    {
        return [
            'typeData.blocks'              => 'required|array|min:1',
            'typeData.blocks.*.block_id'   => 'required|string|max:100',
            'typeData.blocks.*.stains'     => 'required|array|min:1',
            'typeData.blocks.*.stains.*'   => 'string|in:' . implode(',', array_keys(self::STAIN_OPTIONS)),
            'typeData.blocks.*.stain_other' => 'nullable|string|max:200',
            'typeData.blocks.*.indication' => 'nullable|string|max:500',
            'typeData.blocks.*.section_count' => 'required|integer|min:1|max:50',
        ];
    }
}
