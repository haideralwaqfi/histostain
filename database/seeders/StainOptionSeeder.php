<?php

namespace Database\Seeders;

use App\Models\StainOption;
use App\StainTypes\Types\IhcType;
use App\StainTypes\Types\SpecialStainType;
use Illuminate\Database\Seeder;

class StainOptionSeeder extends Seeder
{
    public function run(): void
    {
        $i = 0;
        foreach (IhcType::ANTIBODY_OPTIONS as $key => $label) {
            StainOption::firstOrCreate(
                ['type' => 'ihc', 'key' => $key],
                ['label' => $label, 'sort_order' => $i++],
            );
        }

        $i = 0;
        foreach (SpecialStainType::STAIN_OPTIONS as $key => $label) {
            StainOption::firstOrCreate(
                ['type' => 'special_stain', 'key' => $key],
                ['label' => $label, 'sort_order' => $i++],
            );
        }
    }
}
