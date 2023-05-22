<?php

namespace App\Policies;

use App\Models\Continent;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ContinentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return ($user->can('view continents'));
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Continent $continent): bool
    {
        return ($user->can('view continents'));
    }

    // There is no create, update, delete operation in this CRUD panel, 
    // no need to define corresponding policy functions

}
