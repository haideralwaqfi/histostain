<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@histostains.local'],
            [
                'name' => 'Lab Admin',
                'password' => Hash::make('admin1234'),
                'role' => UserRole::Admin,
                'status' => UserStatus::Approved,
                'email_verified_at' => now(),
            ]
        );
    }
}
