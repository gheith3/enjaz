<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\LeaderQuote;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class LeaderQuotePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LeaderQuote');
    }

    public function view(AuthUser $authUser, LeaderQuote $leaderQuote): bool
    {
        return $authUser->can('View:LeaderQuote');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LeaderQuote');
    }

    public function update(AuthUser $authUser, LeaderQuote $leaderQuote): bool
    {
        return $authUser->can('Update:LeaderQuote');
    }

    public function delete(AuthUser $authUser, LeaderQuote $leaderQuote): bool
    {
        return $authUser->can('Delete:LeaderQuote');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:LeaderQuote');
    }

    public function restore(AuthUser $authUser, LeaderQuote $leaderQuote): bool
    {
        return $authUser->can('Restore:LeaderQuote');
    }

    public function forceDelete(AuthUser $authUser, LeaderQuote $leaderQuote): bool
    {
        return $authUser->can('ForceDelete:LeaderQuote');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LeaderQuote');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LeaderQuote');
    }

    public function replicate(AuthUser $authUser, LeaderQuote $leaderQuote): bool
    {
        return $authUser->can('Replicate:LeaderQuote');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LeaderQuote');
    }
}
