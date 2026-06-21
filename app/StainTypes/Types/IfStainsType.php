<?php

namespace App\StainTypes\Types;

use App\Models\StainOption;
use App\StainTypes\Contracts\StainTypeDefinition;

class IfStainsType implements StainTypeDefinition
{
    public const PANEL_OPTIONS = [
        'iga'    => 'IgA',
        'igm'    => 'IgM',
        'igg'    => 'IgG',
        'c3'     => 'C3',
        'c4'     => 'C4',
        'kappa'  => 'Kappa',
        'lambda' => 'Lambda',
        'other'  => 'Other',
    ];

    public static function options(): array
    {
        return StainOption::optionsArray('if_stains');
    }

    public static function allOptionsWithMeta(): array
    {
        return StainOption::allOptionsWithMeta('if_stains');
    }

    public function label(): string { return 'IF Stains'; }

    public function supportsMultipleBlocks(): bool { return true; }

    public function formPartial(): string { return 'livewire.partials.stain-types.if-stains'; }

    public function defaultData(): array
    {
        return [
            'blocks' => [
                [
                    'block_id'    => '',
                    'panel'       => [],
                    'panel_other' => '',
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
            'typeData.blocks.*.panel'       => 'required|array|min:1',
            'typeData.blocks.*.panel.*'     => 'string|in:' . implode(',', array_keys(StainOption::optionsArray('if_stains'))),
            'typeData.blocks.*.panel_other' => 'nullable|string|max:200',
            'typeData.blocks.*.fixation'    => 'nullable|string|max:200',
            'typeData.blocks.*.indication'  => 'nullable|string|max:500',
        ];
    }
}
