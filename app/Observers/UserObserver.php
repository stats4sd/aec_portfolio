<?php

namespace App\Observers;

use App\Models\Invite;
use App\Models\RoleInvite;
use App\Models\User;
use Illuminate\Support\Str;

class UserObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        // if the user was invited to a team, add them to that team and confirm the invite
        $invites = Invite::where('email', '=', $user->email)->get();
        foreach ($invites as $invite) {
            $user->organisations()->syncWithoutDetaching($invite->organisation->id);

            $invite->confirm();
        }

        $roleInvites = RoleInvite::withoutGlobalScope('unconfirmed')->where('email', '=', $user->email)->get();

        foreach ($roleInvites as $invite) {
            $user->roles()->syncWithoutDetaching($invite->role->id);

            $invite->confirm();
        }
    }
}
