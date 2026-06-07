<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class StainOption extends Model
{
    protected $fillable = ['type', 'key', 'label', 'sort_order'];

    public static function optionsArray(string $type): array
    {
        return Cache::remember("stain_options_{$type}", 3600, function () use ($type) {
            return static::where('type', $type)
                ->orderBy('sort_order')
                ->orderBy('label')
                ->pluck('label', 'key')
                ->toArray();
        });
    }

    public static function clearCache(string $type): void
    {
        Cache::forget("stain_options_{$type}");
    }
}
