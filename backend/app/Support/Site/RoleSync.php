<?php

namespace App\Support\Site;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Dashboard roles and their default permissions.
 *
 * - super_admin: everything (Shield gate), including users and roles.
 * - admin: all content, pages, menus and settings; not users/roles; nothing permanent (force delete).
 * - editor: content, pages and menus; no settings, redirects, social accounts, users, roles or log.
 *
 * Safe to run on every deploy: new permissions (from new resources) are created and given to the roles
 * by these rules, but permissions an admin removed from a role in the dashboard are not added back.
 */
class RoleSync
{
    private const EXCLUDED = [
        'admin' => ['User', 'Role'],
        'editor' => ['User', 'Role', 'Activity', 'Redirect', 'SocialLink', 'ManageGeneralSettings', 'ManageSeoSettings'],
    ];

    public static function run(): void
    {
        $before = Permission::query()->pluck('name')->all();
        Artisan::call('shield:generate', ['--all' => true, '--option' => 'permissions', '--panel' => 'admin']);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $all = Permission::all();
        $new = $all->reject(fn (Permission $p) => in_array($p->name, $before, true));

        Role::findOrCreate('super_admin', 'web');

        foreach (self::EXCLUDED as $name => $excluded) {
            $role = Role::query()->where('name', $name)->where('guard_name', 'web')->first();
            $grant = ($role ? $new : $all)->reject(fn (Permission $p) => self::excluded($p->name, $excluded));
            ($role ?? Role::findOrCreate($name, 'web'))->givePermissionTo($grant);
        }
    }

    /** @param  list<string>  $subjects */
    private static function excluded(string $permission, array $subjects): bool
    {
        [$action, $subject] = array_pad(explode(':', $permission, 2), 2, '');

        return in_array($subject, $subjects, true) || Str::startsWith($action, 'ForceDelete');
    }
}
