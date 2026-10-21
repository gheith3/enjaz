<?php

namespace Database\Seeders;

use App\Enums\AppUserRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $this->createPlatformRoles();
        $this->grantMcpPermission();
    }

    private function createPlatformRoles(): void
    {
        foreach (AppUserRole::cases() as $platformRole) {
            Role::firstOrCreate([
                'name' => $platformRole->value,
                'guard_name' => 'web',
            ]);
        }
    }

    /**
     * MCP (AI assistant) access is a deliberate grant: super admins and admins
     * only, never the editor role (PoliciesAndPermissions excludes ':Mcp').
     */
    private function grantMcpPermission(): void
    {
        $permission = Permission::firstOrCreate([
            'name' => 'Use:Mcp',
            'guard_name' => 'web',
        ]);

        foreach ([AppUserRole::SuperAdmin, AppUserRole::Admin, AppUserRole::FullMcp] as $role) {
            Role::where('name', $role->value)
                ->where('guard_name', 'web')
                ->first()
                ?->givePermissionTo($permission);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
