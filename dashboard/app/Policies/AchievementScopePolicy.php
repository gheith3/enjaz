<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\AchievementScope;
use Illuminate\Auth\Access\HandlesAuthorization;

class AchievementScopePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AchievementScope');
    }

    public function view(AuthUser $authUser, AchievementScope $achievementScope): bool
    {
        return $authUser->can('View:AchievementScope');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AchievementScope');
    }

    public function update(AuthUser $authUser, AchievementScope $achievementScope): bool
    {
        return $authUser->can('Update:AchievementScope');
    }

    public function delete(AuthUser $authUser, AchievementScope $achievementScope): bool
    {
        return $authUser->can('Delete:AchievementScope');
    }

    public function restore(AuthUser $authUser, AchievementScope $achievementScope): bool
    {
        return $authUser->can('Restore:AchievementScope');
    }

    public function forceDelete(AuthUser $authUser, AchievementScope $achievementScope): bool
    {
        return $authUser->can('ForceDelete:AchievementScope');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AchievementScope');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AchievementScope');
    }

    public function replicate(AuthUser $authUser, AchievementScope $achievementScope): bool
    {
        return $authUser->can('Replicate:AchievementScope');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AchievementScope');
    }

}