<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VideoMedia;
use Illuminate\Auth\Access\HandlesAuthorization;

class VideoMediaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_video::media');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, VideoMedia $videoMedia): bool
    {
        return $user->can('view_video::media');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_video::media');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, VideoMedia $videoMedia): bool
    {
        return $user->can('update_video::media');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, VideoMedia $videoMedia): bool
    {
        return $user->can('delete_video::media');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_video::media');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, VideoMedia $videoMedia): bool
    {
        return $user->can('force_delete_video::media');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_video::media');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, VideoMedia $videoMedia): bool
    {
        return $user->can('restore_video::media');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_video::media');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, VideoMedia $videoMedia): bool
    {
        return $user->can('replicate_video::media');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_video::media');
    }
}
