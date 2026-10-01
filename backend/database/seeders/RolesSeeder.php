<?php

namespace Database\Seeders;

use App\Enums\AppUserRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $this->createPlatformRoles();

        //seed permissins
        $this->command->call("shield:generate", [
            "--all" => true,
            "--panel" => "admin",
            "--option" => "policies_and_permissions",
        ]);
    }



    private function createPlatformRoles(): void
    {
        // Iterate over each platform role
        foreach (AppUserRole::cases() as $platformRole) {
            // Create a new role if it doesn't exist, using the role name and 'web' guard
            Role::firstOrCreate([
                'name' => $platformRole->value, // Use the role value as the name
                'guard_name' => 'web', // Use the 'web' guard for this role
            ]);
        }
    }
}
