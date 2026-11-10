<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AwardWinner;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class AwardWinnerPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AwardWinner');
    }

    public function view(AuthUser $authUser, AwardWinner $awardWinner): bool
    {
        return $authUser->can('View:AwardWinner');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AwardWinner');
    }

    public function update(AuthUser $authUser, AwardWinner $awardWinner): bool
    {
        return $authUser->can('Update:AwardWinner');
    }

    public function delete(AuthUser $authUser, AwardWinner $awardWinner): bool
    {
        return $authUser->can('Delete:AwardWinner');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AwardWinner');
    }

    public function restore(AuthUser $authUser, AwardWinner $awardWinner): bool
    {
        return $authUser->can('Restore:AwardWinner');
    }

    public function forceDelete(AuthUser $authUser, AwardWinner $awardWinner): bool
    {
        return $authUser->can('ForceDelete:AwardWinner');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AwardWinner');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AwardWinner');
    }

    public function replicate(AuthUser $authUser, AwardWinner $awardWinner): bool
    {
        return $authUser->can('Replicate:AwardWinner');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AwardWinner');
    }
}
