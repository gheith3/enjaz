<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AwardEdition;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class AwardEditionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AwardEdition');
    }

    public function view(AuthUser $authUser, AwardEdition $awardEdition): bool
    {
        return $authUser->can('View:AwardEdition');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AwardEdition');
    }

    public function update(AuthUser $authUser, AwardEdition $awardEdition): bool
    {
        return $authUser->can('Update:AwardEdition');
    }

    public function delete(AuthUser $authUser, AwardEdition $awardEdition): bool
    {
        return $authUser->can('Delete:AwardEdition');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AwardEdition');
    }

    public function restore(AuthUser $authUser, AwardEdition $awardEdition): bool
    {
        return $authUser->can('Restore:AwardEdition');
    }

    public function forceDelete(AuthUser $authUser, AwardEdition $awardEdition): bool
    {
        return $authUser->can('ForceDelete:AwardEdition');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AwardEdition');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AwardEdition');
    }

    public function replicate(AuthUser $authUser, AwardEdition $awardEdition): bool
    {
        return $authUser->can('Replicate:AwardEdition');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AwardEdition');
    }
}
