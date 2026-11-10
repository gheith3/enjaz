<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Structure only (roles, first admin, the fixed pages and their sections). Same as `site:sync`.
     * Content comes from the dashboard or, in the dev copy, from `php artisan injazat:import-design`.
     *
     * First admin: ADMIN_EMAIL / ADMIN_PASSWORD from the environment. Locally the defaults are
     * admin@example.com / password. In production a random password is set when ADMIN_PASSWORD is empty.
     */
    public function run(): void
    {
        $this->call(RolesSeeder::class);

        $email = env('ADMIN_EMAIL') ?: 'admin@example.com';
        $password = env('ADMIN_PASSWORD') ?: (app()->isProduction() ? Str::random(24) : 'password');

        User::firstOrCreate(
            ['email' => $email],
            ['name' => 'Admin', 'password' => $password, 'email_verified_at' => now()],
        )->assignRole('super_admin');

        Page::syncFromConfig();
    }
}
