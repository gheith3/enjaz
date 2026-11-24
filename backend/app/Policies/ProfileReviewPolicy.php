<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ProfileReview;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

/**
 * Reviews change achievers and their achievements, so they follow the achiever permissions
 * (no separate Shield permissions to grant).
 */
class ProfileReviewPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Achiever');
    }

    public function view(AuthUser $authUser, ProfileReview $profileReview): bool
    {
        return $authUser->can('View:Achiever');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Update:Achiever');
    }

    public function update(AuthUser $authUser, ProfileReview $profileReview): bool
    {
        return $authUser->can('Update:Achiever');
    }

    public function delete(AuthUser $authUser, ProfileReview $profileReview): bool
    {
        return $authUser->can('Update:Achiever');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('Update:Achiever');
    }
}
