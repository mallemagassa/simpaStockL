<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Out;
use Illuminate\Auth\Access\HandlesAuthorization;

class OutPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_out');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Out $out): bool
    {
        return $user->can('view_out');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_out');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Out $out): bool
    {
        return $user->can('update_out');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Out $out): bool
    {
        return $user->can('delete_out');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_out');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Out $out): bool
    {
        return $user->can('force_delete_out');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_out');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Out $out): bool
    {
        return $user->can('restore_out');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_out');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Out $out): bool
    {
        return $user->can('replicate_out');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_out');
    }
}
