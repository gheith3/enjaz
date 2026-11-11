<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Writer;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class WriterPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Writer');
    }

    public function view(AuthUser $authUser, Writer $writer): bool
    {
        return $authUser->can('View:Writer');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Writer');
    }

    public function update(AuthUser $authUser, Writer $writer): bool
    {
        return $authUser->can('Update:Writer');
    }

    public function delete(AuthUser $authUser, Writer $writer): bool
    {
        return $authUser->can('Delete:Writer');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Writer');
    }

    public function restore(AuthUser $authUser, Writer $writer): bool
    {
        return $authUser->can('Restore:Writer');
    }

    public function forceDelete(AuthUser $authUser, Writer $writer): bool
    {
        return $authUser->can('ForceDelete:Writer');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Writer');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Writer');
    }

    public function replicate(AuthUser $authUser, Writer $writer): bool
    {
        return $authUser->can('Replicate:Writer');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Writer');
    }
}
