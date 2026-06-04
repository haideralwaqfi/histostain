<?php

namespace App\StainTypes\Types;

use App\StainTypes\Contracts\StainTypeDefinition;

class DecalcificationType implements StainTypeDefinition
{
    public const METHOD_OPTIONS = [
        'edta' => 'EDTA',
        'acid' => 'Acid',
        'other' => 'Other',
    ];

    public function label(): string { return 'Decalcification'; }

    public function supportsMultipleBlocks(): bool { return true; }

    public function formPartial(): string { return 'livewire.partials.stain-types.decalcification'; }

    public function defaultData(): array
    {
        return [
            'blocks' => [
                [
                    'block_id' => '',
                    'tissue_type' => '',
                    'method' => '',
                    'estimated_time' => '',
                    'indication' => '',
                ],
            ],
        ];
    }

    public function rules(): array
    {
        return [
            'typeData.blocks' => 'required|array|min:1',
            'typeData.blocks.*.block_id' => 'required|string|max:100',
            'typeData.blocks.*.tissue_type' => 'required|string|max:200',
            'typeData.blocks.*.method' => 'required|string|in:' . implode(',', array_keys(self::METHOD_OPTIONS)),
            'typeData.blocks.*.estimated_time' => 'nullable|string|max:100',
            'typeData.blocks.*.indication' => 'required|string|max:500',
        ];
    }
}
