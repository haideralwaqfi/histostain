<?php

namespace App\StainTypes\Types;

use App\Models\StainOption;
use App\StainTypes\Contracts\StainTypeDefinition;

class IhcType implements StainTypeDefinition
{
    public const ANTIBODY_OPTIONS = [
        // ── Lymphoid / Haematopoietic ────────────────────────────────────
        'cd1a'           => 'CD1a',
        'cd2'            => 'CD2',
        'cd3'            => 'CD3',
        'cd4'            => 'CD4',
        'cd5'            => 'CD5',
        'cd7'            => 'CD7',
        'cd8'            => 'CD8',
        'cd10'           => 'CD10',
        'cd11c'          => 'CD11c',
        'cd15'           => 'CD15',
        'cd16'           => 'CD16',
        'cd19'           => 'CD19',
        'cd20'           => 'CD20',
        'cd21'           => 'CD21',
        'cd22'           => 'CD22',
        'cd23'           => 'CD23',
        'cd25'           => 'CD25',
        'cd30'           => 'CD30',
        'cd33'           => 'CD33',
        'cd34'           => 'CD34',
        'cd38'           => 'CD38',
        'cd43'           => 'CD43',
        'cd45'           => 'CD45',
        'cd45ro'         => 'CD45RO',
        'cd56'           => 'CD56',
        'cd57'           => 'CD57',
        'cd61'           => 'CD61',
        'cd68'           => 'CD68',
        'cd79a'          => 'CD79a',
        'cd99'           => 'CD99',
        'cd103'          => 'CD103',
        'cd117'          => 'CD117',
        'cd123'          => 'CD123',
        'cd138'          => 'CD138',
        'cd163'          => 'CD163',
        'cd235a'         => 'CD235a (Glycophorin A)',
        'bcl2'           => 'BCL2',
        'bcl6'           => 'BCL6',
        'mum1'           => 'MUM1',
        'pax5'           => 'PAX5',
        'tdt'            => 'TdT',
        'alk'            => 'ALK',
        'cyclin_d1'      => 'Cyclin D1',
        'sox11'          => 'SOX11',
        'lef1'           => 'LEF1',
        'mnda'           => 'MNDA',
        'irf4'           => 'IRF4',
        'foxp1'          => 'FOXP1',
        'myd88'          => 'MyD88 (L265P)',
        'ebv_lmp1'       => 'EBV-LMP1',
        'ebv_eber'       => 'EBV (EBER ISH)',

        // ── Cytokeratins / Epithelial ─────────────────────────────────────
        'ae1_ae3'        => 'AE1/AE3',
        'cam52'          => 'CAM5.2',
        'ema'            => 'EMA (MUC1)',
        'ck5_6'          => 'CK5/6',
        'ck7'            => 'CK7',
        'ck14'           => 'CK14',
        'ck17'           => 'CK17',
        'ck18'           => 'CK18',
        'ck19'           => 'CK19',
        'ck20'           => 'CK20',
        'ck34be12'       => 'CK34βE12 (HMWCK)',
        'p40'            => 'p40',
        'p63'            => 'p63',
        'p16'            => 'p16',
        'p53'            => 'p53',
        'e_cadherin'     => 'E-Cadherin',

        // ── Breast ────────────────────────────────────────────────────────
        'er'             => 'ER',
        'pr'             => 'PR',
        'her2'           => 'HER2',
        'ar'             => 'AR',
        'gcdfp15'        => 'GCDFP-15',
        'mammaglobin'    => 'Mammaglobin',
        'gata3'          => 'GATA3',

        // ── Lung / Thyroid ────────────────────────────────────────────────
        'ttf1'           => 'TTF-1',
        'napsin_a'       => 'Napsin A',
        'sp_a'           => 'SP-A',

        // ── Neuroendocrine ────────────────────────────────────────────────
        'chromogranin_a' => 'Chromogranin A',
        'synaptophysin'  => 'Synaptophysin',
        'insm1'          => 'INSM1',
        'neun'           => 'NeuN',

        // ── Melanoma / Neural Crest ───────────────────────────────────────
        's100'           => 'S100',
        'sox10'          => 'SOX10',
        'hmb45'          => 'HMB-45',
        'melan_a'        => 'Melan-A',
        'mitf'           => 'MITF',
        'tyrosinase'     => 'Tyrosinase',

        // ── Mesenchymal / Soft Tissue ─────────────────────────────────────
        'vimentin'       => 'Vimentin',
        'desmin'         => 'Desmin',
        'sma'            => 'SMA',
        'msa'            => 'MSA',
        'calponin'       => 'Calponin',
        'h_caldesmon'    => 'h-Caldesmon',
        'myogenin'       => 'Myogenin',
        'myod1'          => 'MyoD1',
        'dog1'           => 'DOG1',
        'stat6'          => 'STAT6',
        'tfe3'           => 'TFE3',
        'nkx22'          => 'NKX2.2',
        'mdm2'           => 'MDM2',
        'cdk4'           => 'CDK4',
        'ss18_ssxn'      => 'SS18-SSX (FISH)',

        // ── Vascular ──────────────────────────────────────────────────────
        'cd31'           => 'CD31',
        'erg'            => 'ERG',
        'fli1'           => 'FLI1',
        'factor_viii'    => 'Factor VIII',
        'd2_40'          => 'D2-40 (Podoplanin)',

        // ── Mismatch Repair (MMR) ─────────────────────────────────────────
        'mlh1'           => 'MLH1',
        'msh2'           => 'MSH2',
        'msh6'           => 'MSH6',
        'pms2'           => 'PMS2',

        // ── GI / Liver ────────────────────────────────────────────────────
        'cdx2'           => 'CDX2',
        'satb2'          => 'SATB2',
        'heppar1'        => 'HepPar-1',
        'arginase1'      => 'Arginase-1',
        'glypican3'      => 'Glypican-3',
        'cea'            => 'CEA',
        'ca19_9'         => 'CA19-9',

        // ── Kidney ────────────────────────────────────────────────────────
        'pax8'           => 'PAX8',
        'pax2'           => 'PAX2',
        'ca_ix'          => 'CA-IX',

        // ── Prostate ──────────────────────────────────────────────────────
        'psa'            => 'PSA',
        'psap'           => 'PSAP',
        'nkx3_1'         => 'NKX3.1',
        'amacr'          => 'AMACR (P504S)',
        'p501s'          => 'P501S (Prostein)',

        // ── Gynaecological ────────────────────────────────────────────────
        'wt1'            => 'WT1',
        'ca125'          => 'CA125',

        // ── Neural / Glial ────────────────────────────────────────────────
        'gfap'           => 'GFAP',
        'olig2'          => 'OLIG2',
        'idh1'           => 'IDH1 (R132H)',
        'atrx'           => 'ATRX',
        'h3k27m'         => 'H3 K27M',
        'h3k27me3'       => 'H3 K27me3',
        'nfp'            => 'NFP',

        // ── Thyroid / Parathyroid ─────────────────────────────────────────
        'thyroglobulin'  => 'Thyroglobulin',
        'calcitonin'     => 'Calcitonin',
        'hbme1'          => 'HBME-1',
        'galectin3'      => 'Galectin-3',
        'braf_v600e'     => 'BRAF V600E (VE1)',

        // ── Adrenal / Gonadal ─────────────────────────────────────────────
        'sf1'            => 'SF-1',
        'inhibin'        => 'Inhibin',
        'calretinin'     => 'Calretinin',

        // ── Histiocytic / Dendritic ───────────────────────────────────────
        'langerin'       => 'Langerin (CD207)',
        'factor_xiii'    => 'Factor XIIIa',
        'fascin'         => 'Fascin',
        'lysozyme'       => 'Lysozyme',

        // ── Proliferation / Molecular ─────────────────────────────────────
        'ki67'           => 'Ki-67',
        'top2a'          => 'Topoisomerase IIα',
        'egfr'           => 'EGFR',
        'ros1'           => 'ROS1',
        'pdl1'           => 'PD-L1',
        'pd1'            => 'PD-1',
        'mmr_deficient'  => 'MMR Deficient (panel)',

        'other'          => 'Other',
    ];

    public static function options(): array
    {
        return StainOption::optionsArray('ihc');
    }

    public static function allOptionsWithMeta(): array
    {
        return StainOption::allOptionsWithMeta('ihc');
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
            'typeData.blocks.*.controls_required'    => 'required|boolean',
        ];
    }
}
