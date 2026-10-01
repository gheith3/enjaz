<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AchievementMainType;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class AchievementMainTypePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AchievementMainType');
    }

    public function view(AuthUser $authUser, AchievementMainType $achievementMainType): bool
    {
        return $authUser->can('View:AchievementMainType');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AchievementMainType');
    }

    public function update(AuthUser $authUser, AchievementMainType $achievementMainType): bool
    {
        return $authUser->can('Update:AchievementMainType');
    }

    public function delete(AuthUser $authUser, AchievementMainType $achievementMainType): bool
    {
        return $authUser->can('Delete:AchievementMainType');
    }

    public function restore(AuthUser $authUser, AchievementMainType $achievementMainType): bool
    {
        return $authUser->can('Restore:AchievementMainType');
    }

    public function forceDelete(AuthUser $authUser, AchievementMainType $achievementMainType): bool
    {
        return $authUser->can('ForceDelete:AchievementMainType');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AchievementMainType');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AchievementMainType');
    }

    public function replicate(AuthUser $authUser, AchievementMainType $achievementMainType): bool
    {
        return $authUser->can('Replicate:AchievementMainType');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AchievementMainType');
    }
}
