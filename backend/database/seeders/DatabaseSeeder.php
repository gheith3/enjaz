<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * First admin: ADMIN_EMAIL / ADMIN_PASSWORD from the environment.
     * Locally the defaults are admin@example.com / password. In production a random password is
     * set when ADMIN_PASSWORD is empty (reset it with `php artisan tinker` or a password reset).
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL') ?: 'admin@example.com';
        $password = env('ADMIN_PASSWORD') ?: (app()->isProduction() ? Str::random(24) : 'password');

        User::firstOrCreate(
            ['email' => $email],
            ['name' => 'Admin', 'password' => $password, 'email_verified_at' => now()],
        );
    }
}
