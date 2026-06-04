<?php

namespace App\StainTypes\Types;

use App\StainTypes\Contracts\StainTypeDefinition;

class FishMolecularType implements StainTypeDefinition
{
    public function label(): string { return 'FISH / Molecular'; }

    public function supportsMultipleBlocks(): bool { return true; }

    public function formPartial(): string { return 'livewire.partials.stain-types.fish-molecular'; }

    public function defaultData(): array
    {
        return [
            'blocks' => [
                [
                    'block_id' => '',
                    'probe' => '',
                    'indication' => '',
                    'send_out_lab' => '',
                    'special_instructions' => '',
                ],
            ],
        ];
    }

    public function rules(): array
    {
        return [
            'typeData.blocks' => 'required|array|min:1',
            'typeData.blocks.*.block_id' => 'required|string|max:100',
            'typeData.blocks.*.probe' => 'required|string|max:200',
            'typeData.blocks.*.indication' => 'required|string|max:500',
            'typeData.blocks.*.send_out_lab' => 'nullable|string|max:200',
            'typeData.blocks.*.special_instructions' => 'nullable|string|max:500',
        ];
    }
}
