<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PublicFigure;
use Illuminate\Auth\Access\HandlesAuthorization;

class PublicFigurePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PublicFigure');
    }

    public function view(AuthUser $authUser, PublicFigure $publicFigure): bool
    {
        return $authUser->can('View:PublicFigure');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PublicFigure');
    }

    public function update(AuthUser $authUser, PublicFigure $publicFigure): bool
    {
        return $authUser->can('Update:PublicFigure');
    }

    public function delete(AuthUser $authUser, PublicFigure $publicFigure): bool
    {
        return $authUser->can('Delete:PublicFigure');
    }

    public function restore(AuthUser $authUser, PublicFigure $publicFigure): bool
    {
        return $authUser->can('Restore:PublicFigure');
    }

    public function forceDelete(AuthUser $authUser, PublicFigure $publicFigure): bool
    {
        return $authUser->can('ForceDelete:PublicFigure');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PublicFigure');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PublicFigure');
    }

    public function replicate(AuthUser $authUser, PublicFigure $publicFigure): bool
    {
        return $authUser->can('Replicate:PublicFigure');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PublicFigure');
    }

}