<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\BusinessSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Creates the business profile and the first administrator.
     * Safe to run more than once.
     */
    public function run(): void
    {
        if (! BusinessSetting::query()->exists()) {
            BusinessSetting::query()->create(BusinessSetting::defaults());
        }

        $email = mb_strtolower((string) env('ADMIN_EMAIL', 'admin@example.com'));

        if (User::query()->where('email', $email)->exists()) {
            $this->command?->info("Administrator {$email} already exists - skipped.");

            return;
        }

        $password = (string) env('ADMIN_PASSWORD', '');
        $generated = $password === '';
        if ($generated) {
            $password = Str::password(16, symbols: false);
        }

        $admin = new User([
            'name' => env('ADMIN_NAME', 'Administrator'),
            'email' => $email,
            'password' => $password,
        ]);
        $admin->role = UserRole::Admin;
        $admin->is_active = true;
        $admin->save();

        $this->command?->info("Administrator created: {$email}");
        if ($generated) {
            $this->command?->warn("Generated password (shown once, change it after logging in): {$password}");
        }
    }
}
