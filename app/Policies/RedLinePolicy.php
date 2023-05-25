<?php

namespace App\Policies;

use App\Models\RedLine;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RedLinePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return ($user->can('view red lines'));
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, RedLine $redLine): bool
    {
        return ($user->can('view red lines'));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return ($user->can('maintain red lines'));
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, RedLine $redLine): bool
    {
        return ($user->can('maintain red lines'));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RedLine $redLine): bool
    {
        return ($user->can('maintain red lines'));
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, RedLine $redLine): bool
    {
        return ($user->can('maintain red lines'));
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, RedLine $redLine): bool
    {
        return ($user->can('maintain red lines'));
    }
}
