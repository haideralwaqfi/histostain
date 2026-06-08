<?php

namespace Database\Seeders;

use App\Enums\StainRequestPriority;
use App\Enums\StainRequestStatus;
use App\Enums\StainRequestType;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\StainRequest;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StainRequestSeeder extends Seeder
{
    public function run(): void
    {
        // ── Users ─────────────────────────────────────────────────────

        $doctor1 = User::firstOrCreate(
            ['email' => 'dr.ali@histostains.local'],
            [
                'name'               => 'Dr. Ali Hassan',
                'password'           => Hash::make('password'),
                'role'               => UserRole::Doctor,
                'status'             => UserStatus::Approved,
                'email_verified_at'  => now(),
            ]
        );

        $doctor2 = User::firstOrCreate(
            ['email' => 'dr.sara@histostains.local'],
            [
                'name'               => 'Dr. Sara Khalid',
                'password'           => Hash::make('password'),
                'role'               => UserRole::Doctor,
                'status'             => UserStatus::Approved,
                'email_verified_at'  => now(),
            ]
        );

        $tech = User::firstOrCreate(
            ['email' => 'tech.omar@histostains.local'],
            [
                'name'               => 'Omar Faris (Tech)',
                'password'           => Hash::make('password'),
                'role'               => UserRole::Tech,
                'status'             => UserStatus::Approved,
                'email_verified_at'  => now(),
            ]
        );

        // ── Requests ──────────────────────────────────────────────────

        $requests = [

            // ── IHC ───────────────────────────────────────────────────

            [
                'doctor_id'   => $doctor1->id,
                'type'        => StainRequestType::Ihc,
                'status'      => StainRequestStatus::Pending,
                'priority'    => StainRequestPriority::Stat,
                'case_number' => 'CASE-2026-001',
                'mrn'         => 'MRN-10041',
                'lab_number'  => 'LAB-8801',
                'notes'       => 'Urgent — suspected high-grade B-cell lymphoma.',
                'type_data'   => [
                    'blocks' => [
                        [
                            'block_id'            => 'A1',
                            'antibodies'          => ['cd20', 'cd3', 'cd10', 'bcl2', 'bcl6', 'ki67'],
                            'antibody_other'      => '',
                            'clone'               => '',
                            'dilution'            => '',
                            'clinical_indication' => 'R/O diffuse large B-cell lymphoma',
                            'section_count'       => 6,
                            'controls_required'   => true,
                        ],
                    ],
                ],
                'created_at'  => now()->subDays(1),
            ],

            [
                'doctor_id'   => $doctor2->id,
                'type'        => StainRequestType::Ihc,
                'status'      => StainRequestStatus::InProgress,
                'priority'    => StainRequestPriority::Urgent,
                'case_number' => 'CASE-2026-002',
                'mrn'         => 'MRN-10055',
                'lab_number'  => 'LAB-8802',
                'notes'       => null,
                'type_data'   => [
                    'blocks' => [
                        [
                            'block_id'            => 'B1',
                            'antibodies'          => ['er', 'pr', 'her2', 'ki67'],
                            'antibody_other'      => '',
                            'clone'               => 'SP1 / 1E2 / 4B5',
                            'dilution'            => 'Prediluted',
                            'clinical_indication' => 'Breast carcinoma — receptor status',
                            'section_count'       => 4,
                            'controls_required'   => true,
                        ],
                        [
                            'block_id'            => 'B2',
                            'antibodies'          => ['ck7', 'gata3'],
                            'antibody_other'      => '',
                            'clone'               => '',
                            'dilution'            => '',
                            'clinical_indication' => 'Confirm breast primary',
                            'section_count'       => 2,
                            'controls_required'   => false,
                        ],
                    ],
                ],
                'created_at'  => now()->subDays(2),
            ],

            [
                'doctor_id'        => $doctor1->id,
                'assigned_tech_id' => $tech->id,
                'type'             => StainRequestType::Ihc,
                'status'           => StainRequestStatus::Completed,
                'priority'         => StainRequestPriority::Routine,
                'case_number'      => 'CASE-2026-003',
                'mrn'              => 'MRN-10062',
                'lab_number'       => 'LAB-8803',
                'notes'            => null,
                'type_data'        => [
                    'blocks' => [
                        [
                            'block_id'            => 'C1',
                            'antibodies'          => ['ttf1', 'ck7', 'ck20', 'cdx2'],
                            'antibody_other'      => '',
                            'clone'               => '',
                            'dilution'            => '',
                            'clinical_indication' => 'Metastatic adenocarcinoma — site of origin',
                            'section_count'       => 4,
                            'controls_required'   => true,
                        ],
                    ],
                ],
                'created_at'       => now()->subDays(5),
            ],

            [
                'doctor_id'   => $doctor2->id,
                'type'        => StainRequestType::Ihc,
                'status'      => StainRequestStatus::Accepted,
                'priority'    => StainRequestPriority::Routine,
                'case_number' => 'CASE-2026-004',
                'mrn'         => 'MRN-10079',
                'lab_number'  => 'LAB-8804',
                'notes'       => null,
                'type_data'   => [
                    'blocks' => [
                        [
                            'block_id'            => 'D1',
                            'antibodies'          => ['s100', 'sox10', 'hmb45', 'melan_a'],
                            'antibody_other'      => '',
                            'clone'               => '',
                            'dilution'            => '',
                            'clinical_indication' => 'Pigmented skin lesion — R/O melanoma',
                            'section_count'       => 4,
                            'controls_required'   => false,
                        ],
                    ],
                ],
                'created_at'  => now()->subHours(10),
            ],

            [
                'doctor_id'   => $doctor1->id,
                'type'        => StainRequestType::Ihc,
                'status'      => StainRequestStatus::Pending,
                'priority'    => StainRequestPriority::Routine,
                'case_number' => 'CASE-2026-005',
                'mrn'         => 'MRN-10083',
                'lab_number'  => null,
                'notes'       => 'MMR panel for Lynch syndrome screening.',
                'type_data'   => [
                    'blocks' => [
                        [
                            'block_id'            => 'E1',
                            'antibodies'          => ['mlh1', 'msh2', 'msh6', 'pms2'],
                            'antibody_other'      => '',
                            'clone'               => '',
                            'dilution'            => '',
                            'clinical_indication' => 'Colorectal adenocarcinoma — MMR status',
                            'section_count'       => 4,
                            'controls_required'   => true,
                        ],
                    ],
                ],
                'created_at'  => now()->subHours(3),
            ],

            // ── Special Stain ─────────────────────────────────────────

            [
                'doctor_id'   => $doctor1->id,
                'type'        => StainRequestType::SpecialStain,
                'status'      => StainRequestStatus::Pending,
                'priority'    => StainRequestPriority::Urgent,
                'case_number' => 'CASE-2026-006',
                'mrn'         => 'MRN-10090',
                'lab_number'  => 'LAB-8810',
                'notes'       => null,
                'type_data'   => [
                    'blocks' => [
                        [
                            'block_id'     => 'F1',
                            'stains'       => ['masson_trichrome', 'reticulin'],
                            'stain_other'  => '',
                            'section_count'=> 2,
                            'indication'   => 'Liver biopsy — staging fibrosis',
                        ],
                    ],
                ],
                'created_at'  => now()->subHours(6),
            ],

            [
                'doctor_id'        => $doctor2->id,
                'assigned_tech_id' => $tech->id,
                'type'             => StainRequestType::SpecialStain,
                'status'           => StainRequestStatus::Completed,
                'priority'         => StainRequestPriority::Routine,
                'case_number'      => 'CASE-2026-007',
                'mrn'              => 'MRN-10102',
                'lab_number'       => 'LAB-8811',
                'notes'            => null,
                'type_data'        => [
                    'blocks' => [
                        [
                            'block_id'     => 'G1',
                            'stains'       => ['ziehl_neelsen', 'grocott'],
                            'stain_other'  => '',
                            'section_count'=> 2,
                            'indication'   => 'Lung biopsy — R/O TB / fungal infection',
                        ],
                        [
                            'block_id'     => 'G2',
                            'stains'       => ['pas'],
                            'stain_other'  => '',
                            'section_count'=> 1,
                            'indication'   => 'PAS for fungal elements',
                        ],
                    ],
                ],
                'created_at'       => now()->subDays(3),
            ],

            [
                'doctor_id'   => $doctor1->id,
                'type'        => StainRequestType::SpecialStain,
                'status'      => StainRequestStatus::InProgress,
                'priority'    => StainRequestPriority::Routine,
                'case_number' => 'CASE-2026-008',
                'mrn'         => 'MRN-10118',
                'lab_number'  => 'LAB-8812',
                'notes'       => null,
                'type_data'   => [
                    'blocks' => [
                        [
                            'block_id'     => 'H1',
                            'stains'       => ['congo_red'],
                            'stain_other'  => '',
                            'section_count'=> 2,
                            'indication'   => 'Cardiac biopsy — R/O amyloidosis',
                        ],
                    ],
                ],
                'created_at'  => now()->subDays(1)->subHours(4),
            ],

            // ── Recut ─────────────────────────────────────────────────

            [
                'doctor_id'   => $doctor2->id,
                'type'        => StainRequestType::Recut,
                'status'      => StainRequestStatus::Pending,
                'priority'    => StainRequestPriority::Routine,
                'case_number' => 'CASE-2026-009',
                'mrn'         => 'MRN-10130',
                'lab_number'  => 'LAB-8820',
                'notes'       => null,
                'type_data'   => [
                    'blocks' => [
                        [
                            'block_id'     => 'I1',
                            'thickness'    => 3,
                            'levels'       => '3 levels, 50µm apart',
                            'reason'       => 'Sections too thick on original — artifact obscuring nuclei',
                            'restain_after'=> true,
                            'restain_stain'=> 'H&E',
                        ],
                    ],
                ],
                'created_at'  => now()->subHours(8),
            ],

            [
                'doctor_id'        => $doctor1->id,
                'assigned_tech_id' => $tech->id,
                'type'             => StainRequestType::Recut,
                'status'           => StainRequestStatus::OnHold,
                'priority'         => StainRequestPriority::Routine,
                'case_number'      => 'CASE-2026-010',
                'mrn'              => 'MRN-10145',
                'lab_number'       => 'LAB-8821',
                'notes'            => 'Block may be exhausted — confirm availability.',
                'type_data'        => [
                    'blocks' => [
                        [
                            'block_id'     => 'J1',
                            'thickness'    => 4,
                            'levels'       => 'Serial sections — 5 levels',
                            'reason'       => 'Looking for micro-invasion — deeper levels required',
                            'restain_after'=> false,
                            'restain_stain'=> '',
                        ],
                    ],
                ],
                'created_at'       => now()->subDays(2),
            ],

            // ── Re-embedding ──────────────────────────────────────────

            [
                'doctor_id'   => $doctor2->id,
                'type'        => StainRequestType::ReEmbedding,
                'status'      => StainRequestStatus::Pending,
                'priority'    => StainRequestPriority::Routine,
                'case_number' => 'CASE-2026-011',
                'mrn'         => 'MRN-10158',
                'lab_number'  => 'LAB-8830',
                'notes'       => null,
                'type_data'   => [
                    'blocks' => [
                        [
                            'block_id'                => 'K1',
                            'reason'                  => 'Tissue embedded at wrong orientation — edge-on instead of face-down',
                            'orientation_instructions'=> 'Re-embed face-down. Confirm orientation before cutting.',
                        ],
                    ],
                ],
                'created_at'  => now()->subHours(5),
            ],

            // ── Decalcification ───────────────────────────────────────

            [
                'doctor_id'   => $doctor1->id,
                'type'        => StainRequestType::Decalcification,
                'status'      => StainRequestStatus::Accepted,
                'priority'    => StainRequestPriority::Routine,
                'case_number' => 'CASE-2026-012',
                'mrn'         => 'MRN-10171',
                'lab_number'  => 'LAB-8840',
                'notes'       => null,
                'type_data'   => [
                    'blocks' => [
                        [
                            'block_id'       => 'L1',
                            'tissue_type'    => 'Bone marrow trephine biopsy',
                            'method'         => 'edta',
                            'estimated_time' => '24h',
                            'indication'     => 'Haematological malignancy workup',
                        ],
                    ],
                ],
                'created_at'  => now()->subDays(1)->subHours(2),
            ],

            [
                'doctor_id'   => $doctor2->id,
                'type'        => StainRequestType::Decalcification,
                'status'      => StainRequestStatus::InProgress,
                'priority'    => StainRequestPriority::Urgent,
                'case_number' => 'CASE-2026-013',
                'mrn'         => 'MRN-10185',
                'lab_number'  => 'LAB-8841',
                'notes'       => 'Rapid decalcification needed for intraoperative consultation.',
                'type_data'   => [
                    'blocks' => [
                        [
                            'block_id'       => 'M1',
                            'tissue_type'    => 'Vertebral bone fragment',
                            'method'         => 'acid',
                            'estimated_time' => '4h',
                            'indication'     => 'Suspected metastatic carcinoma',
                        ],
                    ],
                ],
                'created_at'  => now()->subHours(2),
            ],

            // ── Cytology Special Stain ─────────────────────────────────

            [
                'doctor_id'   => $doctor1->id,
                'type'        => StainRequestType::CytologySpecialStain,
                'status'      => StainRequestStatus::Pending,
                'priority'    => StainRequestPriority::Routine,
                'case_number' => 'CASE-2026-014',
                'mrn'         => 'MRN-10200',
                'lab_number'  => 'LAB-8850',
                'notes'       => null,
                'type_data'   => [
                    'slide_id'   => 'SL-001',
                    'stain'      => 'pap',
                    'stain_other'=> '',
                    'indication' => 'Cervical smear — routine screening',
                ],
                'created_at'  => now()->subHours(1),
            ],

            [
                'doctor_id'        => $doctor2->id,
                'assigned_tech_id' => $tech->id,
                'type'             => StainRequestType::CytologySpecialStain,
                'status'           => StainRequestStatus::Completed,
                'priority'         => StainRequestPriority::Routine,
                'case_number'      => 'CASE-2026-015',
                'mrn'              => 'MRN-10213',
                'lab_number'       => 'LAB-8851',
                'notes'            => null,
                'type_data'        => [
                    'slide_id'   => 'SL-002',
                    'stain'      => 'diff_quik',
                    'stain_other'=> '',
                    'indication' => 'FNA thyroid nodule — rapid assessment',
                ],
                'created_at'       => now()->subDays(4),
            ],

            // ── FISH / Molecular ──────────────────────────────────────

            [
                'doctor_id'   => $doctor1->id,
                'type'        => StainRequestType::FishMolecular,
                'status'      => StainRequestStatus::Pending,
                'priority'    => StainRequestPriority::Stat,
                'case_number' => 'CASE-2026-016',
                'mrn'         => 'MRN-10228',
                'lab_number'  => 'LAB-8860',
                'notes'       => 'STAT — send-out lab requires tissue by EOD.',
                'type_data'   => [
                    'blocks' => [
                        [
                            'block_id'             => 'N1',
                            'probe'                => 'HER2 (17q12)',
                            'indication'           => 'Breast carcinoma — HER2 amplification by FISH',
                            'send_out_lab'         => 'King Fahad Reference Lab',
                            'special_instructions' => 'Minimum 2 unstained slides at 4µm on charged slides.',
                        ],
                    ],
                ],
                'created_at'  => now()->subHours(4),
            ],

            [
                'doctor_id'   => $doctor2->id,
                'type'        => StainRequestType::FishMolecular,
                'status'      => StainRequestStatus::Accepted,
                'priority'    => StainRequestPriority::Urgent,
                'case_number' => 'CASE-2026-017',
                'mrn'         => 'MRN-10240',
                'lab_number'  => 'LAB-8861',
                'notes'       => null,
                'type_data'   => [
                    'blocks' => [
                        [
                            'block_id'             => 'O1',
                            'probe'                => 'ALK (2p23) break-apart',
                            'indication'           => 'Lung adenocarcinoma — ALK rearrangement',
                            'send_out_lab'         => '',
                            'special_instructions' => '',
                        ],
                        [
                            'block_id'             => 'O2',
                            'probe'                => 'ROS1 (6q22) break-apart',
                            'indication'           => 'Concurrent ROS1 testing',
                            'send_out_lab'         => '',
                            'special_instructions' => '',
                        ],
                    ],
                ],
                'created_at'  => now()->subDays(1)->subHours(6),
            ],

        ];

        foreach ($requests as $data) {
            StainRequest::create($data);
        }
    }
}
