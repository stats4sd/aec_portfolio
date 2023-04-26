<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return ($user->can('view projects'));
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Project $project): bool
    {
        return ($user->can('view projects'));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return ($user->can('maintain projects'));
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Project $project): bool
    {
        return ($user->can('maintain projects'));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): bool
    {
        return ($user->can('maintain projects'));
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Project $project): bool
    {
        return ($user->can('maintain projects'));
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Project $project): bool
    {
        return ($user->can('maintain projects'));
    }

    /**
     * Determine whether the user can review redlines the model.
     */
    public function reviewRedlines(User $user, Project $project): bool
    {
        return ($user->can('review redlines'));
    }

    /**
     * Determine whether the user can assess project the model.
     */
    public function assessProject(User $user, Project $project): bool
    {
        return ($user->can('assess project'));
    }

}
