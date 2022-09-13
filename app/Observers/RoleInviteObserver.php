<?php

namespace App\Observers;

use App\Models\RoleInvite;
use App\Mail\RoleInviteMember;
use Illuminate\Support\Facades\Mail;

class RoleInviteObserver
{
    /**
     * Handle the RoleInvite "created" event.
     *
     * @param  \App\Models\Invite  $invite
     * @return void
     */
    public function created(RoleInvite $invite)
    {
        Mail::to($invite->email)->send(new RoleInviteMember($invite));
    }
}
