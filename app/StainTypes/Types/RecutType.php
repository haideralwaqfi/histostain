<?php

namespace App\StainTypes\Types;

use App\StainTypes\Contracts\StainTypeDefinition;

class RecutType implements StainTypeDefinition
{
    public function label(): string { return 'Recut'; }

    public function supportsMultipleBlocks(): bool { return true; }

    public function formPartial(): string { return 'livewire.partials.stain-types.recut'; }

    public function defaultData(): array
    {
        return [
            'blocks' => [
                [
                    'block_id' => '',
                    'levels' => '',
                    'thickness' => '',
                    'reason' => '',
                    'restain_after' => false,
                    'restain_stain' => '',
                ],
            ],
        ];
    }

    public function rules(): array
    {
        return [
            'typeData.blocks' => 'required|array|min:1',
            'typeData.blocks.*.block_id' => 'required|string|max:100',
            'typeData.blocks.*.levels' => 'required|string|max:200',
            'typeData.blocks.*.thickness' => 'nullable|numeric|min:1|max:20',
            'typeData.blocks.*.reason' => 'required|string|max:500',
            'typeData.blocks.*.restain_after' => 'required|boolean',
            'typeData.blocks.*.restain_stain' => 'nullable|required_if:typeData.blocks.*.restain_after,true|string|max:200',
        ];
    }
}
