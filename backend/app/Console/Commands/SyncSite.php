<?php

namespace App\Console\Commands;

use App\Models\Page;
use App\Models\PageSection;
use App\Models\User;
use App\Support\Site\RoleSync;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

/**
 * Brings the site structure in line with the code. Idempotent; the container runs it after every
 * migration (docker/entrypoint.sh), so a deploy that adds resources or page sections needs no manual step.
 */
#[Signature('site:sync')]
#[Description('Sync roles/permissions, the fixed pages and their sections, and the first super admin')]
class SyncSite extends Command
{
    public function handle(): int
    {
        RoleSync::run();
        Page::syncFromConfig();

        $email = config('site.admin_email');
        $hasSuperAdmin = Role::query()->where('name', 'super_admin')->first()?->users()->exists();
        if (! $hasSuperAdmin && $email && ($admin = User::query()->where('email', $email)->first())) {
            $admin->assignRole('super_admin');
            $this->components->info("$email is now super admin.");
        }

        $this->components->info(Role::count().' roles, '.Page::count().' pages, '.PageSection::count().' sections.');

        return self::SUCCESS;
    }
}
