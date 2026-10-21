<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PublicationMedia;
use Illuminate\Auth\Access\HandlesAuthorization;

class PublicationMediaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PublicationMedia');
    }

    public function view(AuthUser $authUser, PublicationMedia $publicationMedia): bool
    {
        return $authUser->can('View:PublicationMedia');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PublicationMedia');
    }

    public function update(AuthUser $authUser, PublicationMedia $publicationMedia): bool
    {
        return $authUser->can('Update:PublicationMedia');
    }

    public function delete(AuthUser $authUser, PublicationMedia $publicationMedia): bool
    {
        return $authUser->can('Delete:PublicationMedia');
    }

    public function restore(AuthUser $authUser, PublicationMedia $publicationMedia): bool
    {
        return $authUser->can('Restore:PublicationMedia');
    }

    public function forceDelete(AuthUser $authUser, PublicationMedia $publicationMedia): bool
    {
        return $authUser->can('ForceDelete:PublicationMedia');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PublicationMedia');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PublicationMedia');
    }

    public function replicate(AuthUser $authUser, PublicationMedia $publicationMedia): bool
    {
        return $authUser->can('Replicate:PublicationMedia');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PublicationMedia');
    }

}