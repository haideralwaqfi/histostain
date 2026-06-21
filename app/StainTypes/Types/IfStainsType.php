<?php

namespace App\StainTypes\Types;

use App\StainTypes\Contracts\StainTypeDefinition;

class IfStainsType implements StainTypeDefinition
{
    public function label(): string { return 'IF Stains'; }

    public function supportsMultipleBlocks(): bool { return true; }

    public function formPartial(): string { return 'livewire.partials.stain-types.if-stains'; }

    public function defaultData(): array
    {
        return [
            'blocks' => [
                [
                    'block_id'    => '',
                    'panel'       => '',
                    'fixation'    => '',
                    'indication'  => '',
                ],
            ],
        ];
    }

    public function rules(): array
    {
        return [
            'typeData.blocks'               => 'required|array|min:1',
            'typeData.blocks.*.block_id'    => 'required|string|max:100',
            'typeData.blocks.*.panel'       => 'nullable|string|max:500',
            'typeData.blocks.*.fixation'    => 'nullable|string|max:200',
            'typeData.blocks.*.indication'  => 'nullable|string|max:500',
        ];
    }
}
