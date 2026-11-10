<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AwardFeature;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class AwardFeaturePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AwardFeature');
    }

    public function view(AuthUser $authUser, AwardFeature $awardFeature): bool
    {
        return $authUser->can('View:AwardFeature');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AwardFeature');
    }

    public function update(AuthUser $authUser, AwardFeature $awardFeature): bool
    {
        return $authUser->can('Update:AwardFeature');
    }

    public function delete(AuthUser $authUser, AwardFeature $awardFeature): bool
    {
        return $authUser->can('Delete:AwardFeature');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AwardFeature');
    }

    public function restore(AuthUser $authUser, AwardFeature $awardFeature): bool
    {
        return $authUser->can('Restore:AwardFeature');
    }

    public function forceDelete(AuthUser $authUser, AwardFeature $awardFeature): bool
    {
        return $authUser->can('ForceDelete:AwardFeature');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AwardFeature');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AwardFeature');
    }

    public function replicate(AuthUser $authUser, AwardFeature $awardFeature): bool
    {
        return $authUser->can('Replicate:AwardFeature');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AwardFeature');
    }
}
