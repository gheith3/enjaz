<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\PageSection;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class PageSectionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PageSection');
    }

    public function view(AuthUser $authUser, PageSection $pageSection): bool
    {
        return $authUser->can('View:PageSection');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PageSection');
    }

    public function update(AuthUser $authUser, PageSection $pageSection): bool
    {
        return $authUser->can('Update:PageSection');
    }

    public function delete(AuthUser $authUser, PageSection $pageSection): bool
    {
        return $authUser->can('Delete:PageSection');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PageSection');
    }

    public function restore(AuthUser $authUser, PageSection $pageSection): bool
    {
        return $authUser->can('Restore:PageSection');
    }

    public function forceDelete(AuthUser $authUser, PageSection $pageSection): bool
    {
        return $authUser->can('ForceDelete:PageSection');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PageSection');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PageSection');
    }

    public function replicate(AuthUser $authUser, PageSection $pageSection): bool
    {
        return $authUser->can('Replicate:PageSection');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PageSection');
    }
}
