<?php

namespace Database\Seeders;

use App\Support\Site\RoleSync;
use Illuminate\Database\Seeder;

/**
 * Dashboard roles and default permissions (see App\Support\Site\RoleSync). Also run by `site:sync`.
 */
class RolesSeeder extends Seeder
{
    public function run(): void
    {
        RoleSync::run();
    }
}
