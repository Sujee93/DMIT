<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed a default admin account for local development.
     * Password is intentionally simple for dev use only — change it before going live.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@katmointeriors.com'],
            [
                'name' => 'Admin',
                'role' => 'admin',
                'password' => 'password',
                'email_verified_at' => now(),
            ]
        );
    }
}
