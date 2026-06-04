<?php

namespace App\Enums;

enum StainRequestStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case InProgress = 'in_progress';
    case OnHold = 'on_hold';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Pending => 'Pending',
            self::Accepted => 'Accepted',
            self::InProgress => 'In Progress',
            self::OnHold => 'On Hold',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Pending => 'amber',
            self::Accepted => 'blue',
            self::InProgress => 'indigo',
            self::OnHold => 'orange',
            self::Completed => 'green',
            self::Cancelled => 'gray',
        };
    }

    /** Returns statuses that this status can transition to for a given role. */
    public function allowedTransitionsFor(UserRole $role): array
    {
        return match($role) {
            UserRole::Tech => match($this) {
                self::Pending => [self::Accepted],
                self::Accepted => [self::InProgress, self::OnHold],
                self::InProgress => [self::Completed, self::OnHold],
                self::OnHold => [self::InProgress],
                default => [],
            },
            UserRole::Doctor => match($this) {
                self::Pending => [self::Cancelled],
                default => [],
            },
            UserRole::Admin => match($this) {
                self::Pending, self::Accepted, self::InProgress, self::OnHold => [self::Cancelled],
                default => [],
            },
        };
    }
}
