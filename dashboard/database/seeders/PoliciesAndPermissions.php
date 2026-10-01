<?php

namespace Database\Seeders;

use App\Enums\AppUserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PoliciesAndPermissions extends Seeder
{
    /**
     * Resource subjects the editor role must never receive: user, role and
     * activity-log management stay reserved for admins.
     */
    private const EDITOR_EXCLUDED_SUBJECTS = [':User', ':Role', ':Activity'];

    public function run(): void
    {
        $this->generatePoliciesAndPermissions();
        $this->syncEditorPermissions();
    }

    private function generatePoliciesAndPermissions(): void
    {
        // use artisan command to generate policies and permissions
        Artisan::call('shield:generate', [
            '--all' => true,
            '--panel' => 'dashboard',
            '--option' => 'policies_and_permissions',
        ]);
    }

    private function syncEditorPermissions(): void
    {
        $editor = Role::where('name', AppUserRole::Editor->value)
            ->where('guard_name', 'web')
            ->first();

        if (! $editor) {
            return;
        }

        $permissions = Permission::where('guard_name', 'web')
            ->get()
            ->reject(
                fn (Permission $permission) => $this->isReservedForAdmins($permission->name)
            );

        $editor->syncPermissions($permissions);
    }

    private function isReservedForAdmins(string $permission): bool
    {
        if (Str::contains($permission, self::EDITOR_EXCLUDED_SUBJECTS)) {
            return true;
        }

        // settings pages (View:ManageGeneralSettings, ...) stay admin-only
        $subject = Str::of($permission)->after(':');

        return $subject->startsWith('Manage') && $subject->endsWith('Settings');
    }
}
