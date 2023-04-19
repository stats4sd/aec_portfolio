<?php

namespace App\Policies;

use App\Models\Organisation;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrganisationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        // return ($user->hasAnyRole('Site Admin')) ? Response::allow()
        // : Response::deny('Sorry, you do not have permissions to view details of all organisations.');;

        // only user with proper permission can view all organisations
        return ($user->hasAnyPermission('view institutions')) ? Response::allow()
        : Response::deny('Sorry, you do not have permissions to view details of all organisations.');;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Organisation  $organisation
     * @return mixed
     */
    public function view(User $user, Organisation $organisation)
    {
        // return $organisation->users->contains($user) || $user->hasAnyRole('Site Admin', 'methods group');

        // only user with proper permission can view organisation model in CRUD panel
        return $user->hasAnyPermission('view institutions');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        // return $user->hasAnyRole('Site Admin');

        // only user with proper permission can create organisation
        return $user->hasAnyPermission('maintain institutions');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Organisation  $organisation
     * @return mixed
     */
    public function update(User $user, Organisation $organisation)
    {
        // return $organisation->admins->contains($user) || $user->hasAnyRole('Site Admin');

        // only user with proper permission can update organisation
        return $user->hasAnyPermission('maintain institutions');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Organisation  $organisation
     * @return mixed
     */
    public function delete(User $user, Organisation $organisation)
    {
        // return $user->hasRole('Site Admin');

        // only user with proper permission can delete organisation
        return $user->hasAnyPermission('maintain institutions');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Organisation  $organisation
     * @return mixed
     */
    public function restore(User $user, Organisation $organisation)
    {
        // return $organisation->admins->contains($user) || $user->hasAnyRole('Site Admin');

        // only user with proper permission can restore organisation
        return $user->hasAnyPermission('maintain institutions');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Organisation  $organisation
     * @return mixed
     */
    public function forceDelete(User $user, Organisation $organisation)
    {
        // return $user->hasAnyRole('Site Admin');

        // only user with proper permission can force delete organisation
        return $user->hasAnyPermission('maintain institutions');
    }

    public function organisationUpdate(User $user, Organisation $organisation)
    {
        // return $organisation->users->contains($user);

        // Question: not quite sure what is the difference between update() and organisationUpdate()...
        return $user->hasAnyPermission('maintain institutions');
    }
}
