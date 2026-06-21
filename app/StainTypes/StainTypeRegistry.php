<?php

namespace App\StainTypes;

use App\Enums\StainRequestType;
use App\StainTypes\Contracts\StainTypeDefinition;
use App\StainTypes\Types\CytologySpecialStainType;
use App\StainTypes\Types\DecalcificationType;
use App\StainTypes\Types\FishMolecularType;
use App\StainTypes\Types\IfStainsType;
use App\StainTypes\Types\IhcType;
use App\StainTypes\Types\ReEmbeddingType;
use App\StainTypes\Types\RecutType;
use App\StainTypes\Types\SpecialStainType;

class StainTypeRegistry
{
    /** @var array<string, StainTypeDefinition> */
    private static array $types = [];

    public static function boot(): void
    {
        self::$types = [
            StainRequestType::Ihc->value => new IhcType(),
            StainRequestType::SpecialStain->value => new SpecialStainType(),
            StainRequestType::Recut->value => new RecutType(),
            StainRequestType::ReEmbedding->value => new ReEmbeddingType(),
            StainRequestType::IfStains->value => new IfStainsType(),
            StainRequestType::Decalcification->value => new DecalcificationType(),
            StainRequestType::CytologySpecialStain->value => new CytologySpecialStainType(),
            StainRequestType::FishMolecular->value => new FishMolecularType(),
        ];
    }

    public static function get(StainRequestType|string $type): StainTypeDefinition
    {
        $key = $type instanceof StainRequestType ? $type->value : $type;

        return self::$types[$key] ?? throw new \InvalidArgumentException("Unknown stain type: {$key}");
    }

    /** @return array<string, string>  key => label pairs for select inputs (excludes deprecated types) */
    public static function options(): array
    {
        return collect(self::$types)
            ->filter(fn($def, $key) => $key !== 'decalcification')
            ->map(fn($def) => $def->label())
            ->all();
    }

    /** @return StainTypeDefinition[] */
    public static function all(): array
    {
        return self::$types;
    }
}
