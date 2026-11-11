<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ContactTopic;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ContactTopicPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ContactTopic');
    }

    public function view(AuthUser $authUser, ContactTopic $contactTopic): bool
    {
        return $authUser->can('View:ContactTopic');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ContactTopic');
    }

    public function update(AuthUser $authUser, ContactTopic $contactTopic): bool
    {
        return $authUser->can('Update:ContactTopic');
    }

    public function delete(AuthUser $authUser, ContactTopic $contactTopic): bool
    {
        return $authUser->can('Delete:ContactTopic');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ContactTopic');
    }

    public function restore(AuthUser $authUser, ContactTopic $contactTopic): bool
    {
        return $authUser->can('Restore:ContactTopic');
    }

    public function forceDelete(AuthUser $authUser, ContactTopic $contactTopic): bool
    {
        return $authUser->can('ForceDelete:ContactTopic');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ContactTopic');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ContactTopic');
    }

    public function replicate(AuthUser $authUser, ContactTopic $contactTopic): bool
    {
        return $authUser->can('Replicate:ContactTopic');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ContactTopic');
    }
}
