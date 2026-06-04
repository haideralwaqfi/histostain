<?php

namespace App\Models;

use App\Enums\StainRequestStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StainRequestTransition extends Model
{
    // Immutable — no updates allowed.
    public $timestamps = false;

    protected $fillable = [
        'stain_request_id',
        'from_status',
        'to_status',
        'performed_by_id',
        'note',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'from_status' => StainRequestStatus::class,
            'to_status' => StainRequestStatus::class,
            'created_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function () {
            throw new \LogicException('StainRequestTransition records are immutable.');
        });
    }

    public function stainRequest(): BelongsTo
    {
        return $this->belongsTo(StainRequest::class);
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by_id');
    }
}
