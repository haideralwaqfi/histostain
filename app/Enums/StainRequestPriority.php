<?php

namespace App\Enums;

enum StainRequestPriority: string
{
    case Routine = 'routine';
    case Urgent = 'urgent';
    case Stat = 'stat';

    public function label(): string
    {
        return match($this) {
            self::Routine => 'Routine',
            self::Urgent => 'Urgent',
            self::Stat => 'STAT',
        };
    }

    public function sortOrder(): int
    {
        return match($this) {
            self::Stat => 0,
            self::Urgent => 1,
            self::Routine => 2,
        };
    }
}
