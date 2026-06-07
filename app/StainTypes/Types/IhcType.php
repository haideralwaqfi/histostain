<?php

namespace App\StainTypes\Types;

use App\Models\StainOption;
use App\StainTypes\Contracts\StainTypeDefinition;

class IhcType implements StainTypeDefinition
{
    public const ANTIBODY_OPTIONS = [
        'cd3'           => 'CD3',
        'cd4'           => 'CD4',
        'cd5'           => 'CD5',
        'cd7'           => 'CD7',
        'cd8'           => 'CD8',
        'cd10'          => 'CD10',
        'cd15'          => 'CD15',
        'cd20'          => 'CD20',
        'cd21'          => 'CD21',
        'cd23'          => 'CD23',
        'cd30'          => 'CD30',
        'cd34'          => 'CD34',
        'cd38'          => 'CD38',
        'cd45'          => 'CD45',
        'cd56'          => 'CD56',
        'cd57'          => 'CD57',
        'cd68'          => 'CD68',
        'cd79a'         => 'CD79a',
        'cd99'          => 'CD99',
        'cd117'         => 'CD117',
        'cd138'         => 'CD138',
        'cd163'         => 'CD163',
        'ki67'          => 'Ki-67',
        'p53'           => 'p53',
        'p63'           => 'p63',
        'p40'           => 'p40',
        'ck7'           => 'CK7',
        'ck20'          => 'CK20',
        'ae1_ae3'       => 'AE1/AE3',
        'ck5_6'         => 'CK5/6',
        'her2'          => 'HER2',
        'er'            => 'ER',
        'pr'            => 'PR',
        'ttf1'          => 'TTF-1',
        's100'          => 'S100',
        'sox10'         => 'SOX10',
        'hmb45'         => 'HMB-45',
        'melan_a'       => 'Melan-A',
        'vimentin'      => 'Vimentin',
        'desmin'        => 'Desmin',
        'sma'           => 'SMA',
        'dog1'          => 'DOG1',
        'chromogranin_a'=> 'Chromogranin A',
        'synaptophysin' => 'Synaptophysin',
        'pax8'          => 'PAX8',
        'cdx2'          => 'CDX2',
        'satb2'         => 'SATB2',
        'bcl2'          => 'BCL2',
        'bcl6'          => 'BCL6',
        'mum1'          => 'MUM1',
        'tdt'           => 'TdT',
        'alk'           => 'ALK',
        'cyclin_d1'     => 'Cyclin D1',
        'gata3'         => 'GATA3',
        'mlh1'          => 'MLH1',
        'msh2'          => 'MSH2',
        'msh6'          => 'MSH6',
        'pms2'          => 'PMS2',
        'pdl1'          => 'PD-L1',
        'other'         => 'Other',
    ];

    public static function options(): array
    {
        return StainOption::optionsArray('ihc');
    }

    public function label(): string { return 'IHC / Immunohistochemistry'; }

    public function supportsMultipleBlocks(): bool { return true; }

    public function formPartial(): string { return 'livewire.partials.stain-types.ihc'; }

    public function defaultData(): array
    {
        return [
            'blocks' => [
                [
                    'block_id'           => '',
                    'antibodies'         => [],
                    'antibody_other'     => '',
                    'clone'              => '',
                    'dilution'           => '',
                    'clinical_indication'=> '',
                    'section_count'      => 1,
                    'controls_required'  => false,
                ],
            ],
        ];
    }

    public function rules(): array
    {
        return [
            'typeData.blocks'                       => 'required|array|min:1',
            'typeData.blocks.*.block_id'             => 'required|string|max:100',
            'typeData.blocks.*.antibodies'           => 'required|array|min:1',
            'typeData.blocks.*.antibodies.*'         => 'string|in:' . implode(',', array_keys(StainOption::optionsArray('ihc'))),
            'typeData.blocks.*.antibody_other'       => 'nullable|string|max:200',
            'typeData.blocks.*.clone'                => 'nullable|string|max:100',
            'typeData.blocks.*.dilution'             => 'nullable|string|max:100',
            'typeData.blocks.*.clinical_indication'  => 'nullable|string|max:500',
            'typeData.blocks.*.section_count'        => 'required|integer|min:1|max:50',
            'typeData.blocks.*.controls_required'    => 'required|boolean',
        ];
    }
}
