<?php

namespace App\StainTypes\Types;

use App\StainTypes\Contracts\StainTypeDefinition;

class ReEmbeddingType implements StainTypeDefinition
{
    public function label(): string { return 'Re-embedding'; }

    public function supportsMultipleBlocks(): bool { return true; }

    public function formPartial(): string { return 'livewire.partials.stain-types.re-embedding'; }

    public function defaultData(): array
    {
        return [
            'blocks' => [
                [
                    'block_id' => '',
                    'reason' => '',
                    'orientation_instructions' => '',
                ],
            ],
        ];
    }

    public function rules(): array
    {
        return [
            'typeData.blocks' => 'required|array|min:1',
            'typeData.blocks.*.block_id' => 'required|string|max:100',
            'typeData.blocks.*.reason' => 'required|string|max:500',
            'typeData.blocks.*.orientation_instructions' => 'nullable|string|max:500',
        ];
    }
}
