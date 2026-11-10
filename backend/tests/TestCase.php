<?php

namespace Tests;

use App\Models\User;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * A dashboard user with the given role (roles and permissions seeded like production).
     */
    protected function userWithRole(?string $role): User
    {
        $this->seed(RolesSeeder::class);
        $user = User::factory()->create();

        return $role ? $user->assignRole($role) : $user;
    }
}
