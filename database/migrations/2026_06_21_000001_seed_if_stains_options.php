<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $options = [
            ['key' => 'iga',    'label' => 'IgA'],
            ['key' => 'igm',    'label' => 'IgM'],
            ['key' => 'igg',    'label' => 'IgG'],
            ['key' => 'c3',     'label' => 'C3'],
            ['key' => 'c4',     'label' => 'C4'],
            ['key' => 'kappa',  'label' => 'Kappa'],
            ['key' => 'lambda', 'label' => 'Lambda'],
            ['key' => 'other',  'label' => 'Other'],
        ];

        foreach ($options as $i => $opt) {
            DB::table('stain_options')->insertOrIgnore([
                'type'       => 'if_stains',
                'key'        => $opt['key'],
                'label'      => $opt['label'],
                'sort_order' => $i,
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('stain_options')->where('type', 'if_stains')->delete();
    }
};
