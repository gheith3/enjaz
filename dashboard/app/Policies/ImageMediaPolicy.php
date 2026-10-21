<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ImageMedia;
use Illuminate\Auth\Access\HandlesAuthorization;

class ImageMediaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ImageMedia');
    }

    public function view(AuthUser $authUser, ImageMedia $imageMedia): bool
    {
        return $authUser->can('View:ImageMedia');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ImageMedia');
    }

    public function update(AuthUser $authUser, ImageMedia $imageMedia): bool
    {
        return $authUser->can('Update:ImageMedia');
    }

    public function delete(AuthUser $authUser, ImageMedia $imageMedia): bool
    {
        return $authUser->can('Delete:ImageMedia');
    }

    public function restore(AuthUser $authUser, ImageMedia $imageMedia): bool
    {
        return $authUser->can('Restore:ImageMedia');
    }

    public function forceDelete(AuthUser $authUser, ImageMedia $imageMedia): bool
    {
        return $authUser->can('ForceDelete:ImageMedia');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ImageMedia');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ImageMedia');
    }

    public function replicate(AuthUser $authUser, ImageMedia $imageMedia): bool
    {
        return $authUser->can('Replicate:ImageMedia');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ImageMedia');
    }

}