<?php

namespace App\Models;

use App\Enums\StainRequestPriority;
use App\Enums\StainRequestStatus;
use App\Enums\StainRequestType;
use App\Enums\UserRole;
use App\Exceptions\StainTransitionException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class StainRequest extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'ulid',
        'doctor_id',
        'assigned_tech_id',
        'type',
        'status',
        'priority',
        'mrn',
        'patient_name',
        'case_number',
        'notes',
        'type_data',
    ];

    protected function casts(): array
    {
        return [
            'type' => StainRequestType::class,
            'status' => StainRequestStatus::class,
            'priority' => StainRequestPriority::class,
            'type_data' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            $model->ulid ??= (string) Str::ulid();
        });
    }

    public function registerMediaCollections(): void
    {
        // Doctor attachments (requisition forms, images) uploaded at request creation
        $this->addMediaCollection('requisition_attachments')
            ->useDisk('spaces');

        // Tech attachments (result images, reports) uploaded at completion
        $this->addMediaCollection('result_attachments')
            ->useDisk('spaces');
    }

    // ── Relationships ──────────────────────────────────────────────

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function assignedTech(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_tech_id');
    }

    public function transitions(): HasMany
    {
        return $this->hasMany(StainRequestTransition::class)->orderBy('created_at');
    }

    // ── Scopes ─────────────────────────────────────────────────────

    public function scopePriorityOrdered($query)
    {
        return $query->orderByRaw(self::priorityOrderSql())->orderBy('created_at');
    }

    /** CASE expression that sorts stat→urgent→routine in both MySQL and SQLite. */
    public static function priorityOrderSql(): string
    {
        return "CASE priority WHEN 'stat' THEN 0 WHEN 'urgent' THEN 1 ELSE 2 END";
    }

    // ── State machine ──────────────────────────────────────────────

    public function canTransitionTo(StainRequestStatus $target, User $actor): bool
    {
        $allowed = $this->status->allowedTransitionsFor($actor->role);
        return in_array($target, $allowed, strict: true);
    }

    /**
     * Transition the request to a new status, recording history.
     * Callers must authorize before calling this method.
     *
     * @throws StainTransitionException
     */
    public function transitionTo(
        StainRequestStatus $target,
        User $actor,
        ?string $note = null
    ): StainRequestTransition {
        if (! $this->canTransitionTo($target, $actor)) {
            throw new StainTransitionException(
                "Cannot transition from [{$this->status->value}] to [{$target->value}] as [{$actor->role?->value}]."
            );
        }

        $from = $this->status;
        $this->status = $target;
        $this->save();

        return $this->transitions()->create([
            'from_status' => $from->value,
            'to_status' => $target->value,
            'performed_by_id' => $actor->id,
            'note' => $note,
            'created_at' => now(),
        ]);
    }

    // ── Helpers ────────────────────────────────────────────────────

    public function isStat(): bool
    {
        return $this->priority === StainRequestPriority::Stat;
    }
}
