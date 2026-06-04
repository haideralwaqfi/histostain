<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Doctor = 'doctor';
    case Tech = 'tech';

    public function label(): string
    {
        return match($this) {
            self::Admin => 'Administrator',
            self::Doctor => 'Doctor',
            self::Tech => 'Technician',
        };
    }
}
