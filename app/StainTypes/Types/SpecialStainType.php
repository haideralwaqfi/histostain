<?php

namespace App\StainTypes\Types;

use App\Models\StainOption;
use App\StainTypes\Contracts\StainTypeDefinition;

class SpecialStainType implements StainTypeDefinition
{
    public const STAIN_OPTIONS = [
        // ── Carbohydrates / Mucins ────────────────────────────────────────
        'pas'                => 'PAS (Periodic Acid-Schiff)',
        'pas_d'              => 'PAS-D (PAS with Diastase)',
        'alcian_blue'        => 'Alcian Blue (pH 2.5)',
        'alcian_blue_ph1'    => 'Alcian Blue (pH 1.0)',
        'alcian_blue_pas'    => 'Alcian Blue / PAS',
        'mucicarmine'        => 'Mucicarmine',
        'colloidal_iron'     => 'Colloidal Iron',
        'high_iron_diamine'  => 'High Iron Diamine (HID)',

        // ── Connective Tissue / Fibrosis ──────────────────────────────────
        'masson_trichrome'   => 'Masson Trichrome',
        'sirius_red'         => 'Sirius Red',
        'reticulin'          => 'Reticulin (Gordon & Sweet)',
        'reticulin_gomori'   => 'Reticulin (Gomori)',
        'van_gieson'         => 'Van Gieson',
        'elastic_vg'         => 'Elastic Van Gieson (EVG)',
        'verhoeff'           => 'Verhoeff-Van Gieson (VVG)',
        'orcein'             => 'Orcein',
        'ptah'               => 'PTAH (Phosphotungstic Acid Haematoxylin)',
        'azan'               => 'Azan (Mallory)',

        // ── Microorganisms ────────────────────────────────────────────────
        'ziehl_neelsen'      => 'Ziehl-Neelsen (ZN / AFB)',
        'fite_faraco'        => 'Fite-Faraco (Modified ZN)',
        'grocott'            => 'Grocott (GMS – Fungi)',
        'pas_fungi'          => 'PAS (Fungi)',
        'giemsa'             => 'Giemsa',
        'warthin_starry'     => 'Warthin-Starry (Spirochetes / H. pylori)',
        'dieterle'           => 'Dieterle (Legionella / Spirochetes)',
        'brown_hopps'        => 'Brown & Hopps (Gram)',
        'gram_twort'         => 'Gram-Twort',
        'auramine_O'         => 'Auramine O (Fluorescent AFB)',

        // ── Pigments & Minerals ───────────────────────────────────────────
        'perls_iron'         => "Perls' Prussian Blue (Iron)",
        'von_kossa'          => 'Von Kossa (Calcium)',
        'alizarin_red'       => 'Alizarin Red S (Calcium)',
        'rhodanine'          => 'Rhodanine (Copper)',
        'rubeanic_acid'      => 'Rubeanic Acid (Copper)',
        'halls_bile'         => "Hall's Bile (Bilirubin)",
        'fontana_masson'     => 'Fontana-Masson (Melanin / Argentaffin)',
        'schmorl'            => "Schmorl's (Lipofuscin / Ceroid)",
        'luxol_fast_blue'    => 'Luxol Fast Blue (LFB – Myelin)',
        'oil_red_o'          => 'Oil Red O (Lipids – frozen)',
        'sudan_black'        => 'Sudan Black B (Lipids)',
        'osmium_tetroxide'   => 'Osmium Tetroxide (Lipids – EM prep)',

        // ── Neural / Neuropathology ───────────────────────────────────────
        'cresyl_violet'      => 'Cresyl Violet (Nissl)',
        'toluidine_blue'     => 'Toluidine Blue',
        'bielschowsky'       => 'Bielschowsky (Silver – Axons/Tangles)',
        'bodian'             => 'Bodian (Silver – Nerve Fibres)',
        'holmes_silver'      => 'Holmes Silver (Axons)',
        'Congo_red'          => 'Congo Red (Amyloid)',
        'crystal_violet'     => 'Crystal Violet (Amyloid)',
        'thioflavin_s'       => 'Thioflavin S (Amyloid – fluorescent)',
        'thioflavin_t'       => 'Thioflavin T (Amyloid – fluorescent)',

        // ── Haematopoietic / Bone Marrow ──────────────────────────────────
        'chloroacetate'      => 'Chloroacetate Esterase (CAE / Leder)',
        'non_specific_est'   => 'Non-Specific Esterase (NSE α-naphthyl)',
        'perl_bone_marrow'   => "Perls' Iron (Bone Marrow)",
        'pas_bone_marrow'    => 'PAS (Bone Marrow / Glycogen)',
        'methyl_green_pyr'   => 'Methyl Green-Pyronin (MGP)',
        'grimelius'          => 'Grimelius (Argyrophil – Neuroendocrine)',

        // ── Renal / Kidney Biopsy ─────────────────────────────────────────
        'jones_silver'       => 'Jones Methenamine Silver (GBM)',
        'pas_renal'          => 'PAS (Renal)',
        'masson_renal'       => 'Masson Trichrome (Renal)',

        // ── Liver Biopsy ──────────────────────────────────────────────────
        'shikata_orcein'     => 'Shikata Orcein (HBsAg / Copper)',
        'victoria_blue'      => 'Victoria Blue (HBsAg)',
        'rhodanine_liver'    => 'Rhodanine (Hepatic Copper)',

        // ── Muscle ────────────────────────────────────────────────────────
        'atpase_94'          => 'ATPase pH 9.4',
        'atpase_46'          => 'ATPase pH 4.6',
        'atpase_43'          => 'ATPase pH 4.3',
        'nadh_tr'            => 'NADH-TR',
        'sdh'                => 'SDH (Succinate Dehydrogenase)',
        'cox'                => 'COX (Cytochrome Oxidase)',
        'cox_sdh'            => 'COX / SDH Sequential',
        'mgt'                => 'Modified Gomori Trichrome (MGT)',
        'acid_phosphatase'   => 'Acid Phosphatase',
        'alkaline_phosphatase' => 'Alkaline Phosphatase',
        'myophosphorylase'   => 'Myophosphorylase (McArdle)',
        'phosphofructokinase' => 'Phosphofructokinase (PFK)',

        'other'              => 'Other',
    ];

    public static function options(): array
    {
        return StainOption::optionsArray('special_stain');
    }

    public static function allOptionsWithMeta(): array
    {
        return StainOption::allOptionsWithMeta('special_stain');
    }

    public function label(): string { return 'Special Stain'; }

    public function supportsMultipleBlocks(): bool { return true; }

    public function formPartial(): string { return 'livewire.partials.stain-types.special-stain'; }

    public function defaultData(): array
    {
        return [
            'blocks' => [
                [
                    'block_id'    => '',
                    'stains'      => [],
                    'stain_other' => '',
                    'indication'  => '',
                    'section_count' => 1,
                ],
            ],
        ];
    }

    public function rules(): array
    {
        return [
            'typeData.blocks'              => 'required|array|min:1',
            'typeData.blocks.*.block_id'   => 'required|string|max:100',
            'typeData.blocks.*.stains'     => 'required|array|min:1',
            'typeData.blocks.*.stains.*'   => 'string|in:' . implode(',', array_keys(StainOption::optionsArray('special_stain'))),
            'typeData.blocks.*.stain_other' => 'nullable|string|max:200',
            'typeData.blocks.*.indication' => 'nullable|string|max:500',
            'typeData.blocks.*.section_count' => 'required|integer|min:1|max:50',
        ];
    }
}
