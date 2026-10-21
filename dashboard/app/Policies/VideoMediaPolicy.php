<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\VideoMedia;
use Illuminate\Auth\Access\HandlesAuthorization;

class VideoMediaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:VideoMedia');
    }

    public function view(AuthUser $authUser, VideoMedia $videoMedia): bool
    {
        return $authUser->can('View:VideoMedia');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:VideoMedia');
    }

    public function update(AuthUser $authUser, VideoMedia $videoMedia): bool
    {
        return $authUser->can('Update:VideoMedia');
    }

    public function delete(AuthUser $authUser, VideoMedia $videoMedia): bool
    {
        return $authUser->can('Delete:VideoMedia');
    }

    public function restore(AuthUser $authUser, VideoMedia $videoMedia): bool
    {
        return $authUser->can('Restore:VideoMedia');
    }

    public function forceDelete(AuthUser $authUser, VideoMedia $videoMedia): bool
    {
        return $authUser->can('ForceDelete:VideoMedia');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:VideoMedia');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:VideoMedia');
    }

    public function replicate(AuthUser $authUser, VideoMedia $videoMedia): bool
    {
        return $authUser->can('Replicate:VideoMedia');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:VideoMedia');
    }

}