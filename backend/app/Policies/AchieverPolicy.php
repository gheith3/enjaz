<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Achiever;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class AchieverPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Achiever');
    }

    public function view(AuthUser $authUser, Achiever $achiever): bool
    {
        return $authUser->can('View:Achiever');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Achiever');
    }

    public function update(AuthUser $authUser, Achiever $achiever): bool
    {
        return $authUser->can('Update:Achiever');
    }

    public function delete(AuthUser $authUser, Achiever $achiever): bool
    {
        return $authUser->can('Delete:Achiever');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Achiever');
    }

    public function restore(AuthUser $authUser, Achiever $achiever): bool
    {
        return $authUser->can('Restore:Achiever');
    }

    public function forceDelete(AuthUser $authUser, Achiever $achiever): bool
    {
        return $authUser->can('ForceDelete:Achiever');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Achiever');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Achiever');
    }

    public function replicate(AuthUser $authUser, Achiever $achiever): bool
    {
        return $authUser->can('Replicate:Achiever');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Achiever');
    }
}
