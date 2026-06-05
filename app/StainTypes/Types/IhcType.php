<?php

namespace App\StainTypes\Types;

use App\StainTypes\Contracts\StainTypeDefinition;

class IhcType implements StainTypeDefinition
{
    public function label(): string { return 'IHC / Immunohistochemistry'; }

    public function supportsMultipleBlocks(): bool { return true; }

    public function formPartial(): string { return 'livewire.partials.stain-types.ihc'; }

    public function defaultData(): array
    {
        return [
            'blocks' => [
                [
                    'block_id' => '',
                    'antibody' => '',
                    'clone' => '',
                    'dilution' => '',
                    'clinical_indication' => '',
                    'section_count' => 1,
                    'controls_required' => false,
                ],
            ],
        ];
    }

    public function rules(): array
    {
        return [
            'typeData.blocks' => 'required|array|min:1',
            'typeData.blocks.*.block_id' => 'required|string|max:100',
            'typeData.blocks.*.antibody' => 'required|string|max:200',
            'typeData.blocks.*.clone' => 'nullable|string|max:100',
            'typeData.blocks.*.dilution' => 'nullable|string|max:100',
            'typeData.blocks.*.clinical_indication' => 'nullable|string|max:500',
            'typeData.blocks.*.section_count' => 'required|integer|min:1|max:50',
            'typeData.blocks.*.controls_required' => 'required|boolean',
        ];
    }
}
