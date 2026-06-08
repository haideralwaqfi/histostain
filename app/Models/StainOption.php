<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class StainOption extends Model
{
    protected $fillable = ['type', 'key', 'label', 'sort_order', 'is_active', 'inactive_reason'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    /** Active options only — keyed by slug, used for form validation. */
    public static function optionsArray(string $type): array
    {
        return Cache::remember("stain_options_{$type}", 3600, function () use ($type) {
            return static::where('type', $type)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('label')
                ->pluck('label', 'key')
                ->toArray();
        });
    }

    /**
     * All options including inactive — keyed by slug, with full meta.
     * Used by form partials to render inactive pills with reason popups.
     *
     * @return array<string, array{label: string, is_active: bool, inactive_reason: string|null}>
     */
    public static function allOptionsWithMeta(string $type): array
    {
        return Cache::remember("stain_options_meta_{$type}", 3600, function () use ($type) {
            return static::where('type', $type)
                ->orderBy('sort_order')
                ->orderBy('label')
                ->get()
                ->keyBy('key')
                ->map(fn($opt) => [
                    'label'           => $opt->label,
                    'is_active'       => $opt->is_active,
                    'inactive_reason' => $opt->inactive_reason,
                ])
                ->toArray();
        });
    }

    public static function clearCache(string $type): void
    {
        Cache::forget("stain_options_{$type}");
        Cache::forget("stain_options_meta_{$type}");
    }
}
