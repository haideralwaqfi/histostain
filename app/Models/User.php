<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasPushSubscriptions;

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
        'role',
        'status',
        'rejection_reason',
        'expo_push_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'status' => UserStatus::class,
        ];
    }

    // ── Relationships ──────────────────────────────────────────────

    public function stainRequests(): HasMany
    {
        return $this->hasMany(StainRequest::class, 'doctor_id');
    }

    public function assignedRequests(): HasMany
    {
        return $this->hasMany(StainRequest::class, 'assigned_tech_id');
    }

    public function performedTransitions(): HasMany
    {
        return $this->hasMany(StainRequestTransition::class, 'performed_by_id');
    }

    // ── Scopes ─────────────────────────────────────────────────────

    public function scopeAdmins($query)
    {
        return $query->where('role', UserRole::Admin);
    }

    public function scopeTechs($query)
    {
        return $query->where('role', UserRole::Tech);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', UserStatus::Approved);
    }

    public function scopePending($query)
    {
        return $query->where('status', UserStatus::Pending);
    }

    // ── Helpers ────────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isDoctor(): bool
    {
        return $this->role === UserRole::Doctor;
    }

    public function isTech(): bool
    {
        return $this->role === UserRole::Tech;
    }

    public function isApproved(): bool
    {
        return $this->status === UserStatus::Approved;
    }

    public function isPending(): bool
    {
        return $this->status === UserStatus::Pending;
    }

    public function isRejected(): bool
    {
        return $this->status === UserStatus::Rejected;
    }

    public function dashboardRoute(): string
    {
        return match($this->role) {
            UserRole::Admin => route('admin.dashboard'),
            UserRole::Doctor => route('doctor.requests'),
            UserRole::Tech => route('tech.queue'),
            default => route('pending'),
        };
    }
}
