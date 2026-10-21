<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SubAchievementType;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubAchievementTypePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SubAchievementType');
    }

    public function view(AuthUser $authUser, SubAchievementType $subAchievementType): bool
    {
        return $authUser->can('View:SubAchievementType');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SubAchievementType');
    }

    public function update(AuthUser $authUser, SubAchievementType $subAchievementType): bool
    {
        return $authUser->can('Update:SubAchievementType');
    }

    public function delete(AuthUser $authUser, SubAchievementType $subAchievementType): bool
    {
        return $authUser->can('Delete:SubAchievementType');
    }

    public function restore(AuthUser $authUser, SubAchievementType $subAchievementType): bool
    {
        return $authUser->can('Restore:SubAchievementType');
    }

    public function forceDelete(AuthUser $authUser, SubAchievementType $subAchievementType): bool
    {
        return $authUser->can('ForceDelete:SubAchievementType');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SubAchievementType');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SubAchievementType');
    }

    public function replicate(AuthUser $authUser, SubAchievementType $subAchievementType): bool
    {
        return $authUser->can('Replicate:SubAchievementType');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SubAchievementType');
    }

}