<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\LabItem;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class LabItemPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LabItem');
    }

    public function view(AuthUser $authUser, LabItem $labItem): bool
    {
        return $authUser->can('View:LabItem');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LabItem');
    }

    public function update(AuthUser $authUser, LabItem $labItem): bool
    {
        return $authUser->can('Update:LabItem');
    }

    public function delete(AuthUser $authUser, LabItem $labItem): bool
    {
        return $authUser->can('Delete:LabItem');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:LabItem');
    }

    public function restore(AuthUser $authUser, LabItem $labItem): bool
    {
        return $authUser->can('Restore:LabItem');
    }

    public function forceDelete(AuthUser $authUser, LabItem $labItem): bool
    {
        return $authUser->can('ForceDelete:LabItem');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LabItem');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LabItem');
    }

    public function replicate(AuthUser $authUser, LabItem $labItem): bool
    {
        return $authUser->can('Replicate:LabItem');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LabItem');
    }
}
