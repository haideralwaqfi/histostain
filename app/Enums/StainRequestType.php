<?php

namespace App\Enums;

enum StainRequestType: string
{
    case Ihc = 'ihc';
    case SpecialStain = 'special_stain';
    case Recut = 'recut';
    case ReEmbedding = 're_embedding';
    case IfStains = 'if_stains';
    case Decalcification = 'decalcification';
    case CytologySpecialStain = 'cytology_special_stain';
    case FishMolecular = 'fish_molecular';

    public function label(): string
    {
        return match($this) {
            self::Ihc => 'IHC / Immunohistochemistry',
            self::SpecialStain => 'Special Stain',
            self::Recut => 'Recut',
            self::ReEmbedding => 'Re-embedding',
            self::IfStains => 'IF Stains',
            self::Decalcification => 'Decalcification',
            self::CytologySpecialStain => 'Cytology Special Stain',
            self::FishMolecular => 'FISH / Molecular',
        };
    }

    public function shortLabel(): string
    {
        return match($this) {
            self::Ihc => 'IHC',
            self::SpecialStain => 'Special Stain',
            self::Recut => 'Recut',
            self::ReEmbedding => 'Re-embed',
            self::IfStains => 'IF Stains',
            self::Decalcification => 'Decalc',
            self::CytologySpecialStain => 'Cytology',
            self::FishMolecular => 'FISH',
        };
    }

    /** Cytology uses slide IDs; all others use block IDs. */
    public function usesSlidId(): bool
    {
        return $this === self::CytologySpecialStain;
    }

    /** Whether type_data.blocks is an array (true) or a single object (false). */
    public function supportsMultipleBlocks(): bool
    {
        return $this !== self::CytologySpecialStain;
    }
}
