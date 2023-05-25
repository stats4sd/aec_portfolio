<?php

namespace App\Policies;

use App\Models\OrganisationMember;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrganisationMemberPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return ($user->can('view institutional members'));
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OrganisationMember $organisationMember): bool
    {
        return ($user->can('view institutional members'));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return ($user->can('invite institutional members'));
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return ($user->can('maintain institutional members'));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return ($user->can('maintain institutional members'));
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, OrganisationMember $organisationMember): bool
    {
        return ($user->can('maintain institutional members'));
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, OrganisationMember $organisationMember): bool
    {
        return ($user->can('maintain institutional members'));
    }
}
