<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true; // project list is filtered by default, so this is ok.
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Project $project
     * @return mixed
     */
    public function view(User $user, Project $project)
    {

        // any member of an organisation can view a project

        return $project->organisation->users->contains($user) || $user->hasAnyRole('Site Admin');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->organisations->some(function ($org) use ($user) {
                return $org->admins->contains($user) || $org->editors->contains($user);
                }) || $user->hasAnyRole('Site Admin');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Project $project
     * @return mixed
     */
    public function update(User $user, Project $project)
    {
        return $project->organisation->admins->contains($user) || $project->organisation->editors->contains($user) || $user->hasAnyRole('Site Admin');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Project $project
     * @return mixed
     */
    public function delete(User $user, Project $project)
    {
        return $project->organisation->admins->contains($user) || $project->organisation->editors->contains($user) || $user->hasAnyRole('Site Admin');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Project $project
     * @return mixed
     */
    public function restore(User $user, Project $project)
    {
        return $project->organisation->admins->contains($user) || $user->hasAnyRole('Site Admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Project $project
     * @return mixed
     */
    public function forceDelete(User $user, Project $project)
    {
        return $user->hasAnyRole('Site Admin');
    }

    public function organisationUpdate(User $user, Project $project)
    {
        return $project->organisation->admins->contains($user);
    }
}
