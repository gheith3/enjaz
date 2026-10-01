<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ContactUsMessage;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ContactUsMessagePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ContactUsMessage');
    }

    public function view(AuthUser $authUser, ContactUsMessage $contactUsMessage): bool
    {
        return $authUser->can('View:ContactUsMessage');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ContactUsMessage');
    }

    public function update(AuthUser $authUser, ContactUsMessage $contactUsMessage): bool
    {
        return $authUser->can('Update:ContactUsMessage');
    }

    public function delete(AuthUser $authUser, ContactUsMessage $contactUsMessage): bool
    {
        return $authUser->can('Delete:ContactUsMessage');
    }

    public function restore(AuthUser $authUser, ContactUsMessage $contactUsMessage): bool
    {
        return $authUser->can('Restore:ContactUsMessage');
    }

    public function forceDelete(AuthUser $authUser, ContactUsMessage $contactUsMessage): bool
    {
        return $authUser->can('ForceDelete:ContactUsMessage');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ContactUsMessage');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ContactUsMessage');
    }

    public function replicate(AuthUser $authUser, ContactUsMessage $contactUsMessage): bool
    {
        return $authUser->can('Replicate:ContactUsMessage');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ContactUsMessage');
    }
}
