<?php

namespace App\Observers;

use App\Models\Invite;
use App\Mail\InviteMember;
use Illuminate\Support\Facades\Mail;

class InviteObserver
{
    /**
     * Handle the Invite "created" event.
     *
     * @param  \App\Models\Invite  $invite
     * @return void
     */
    public function created(Invite $invite)
    {
        Mail::to($invite->email)->send(new InviteMember($invite));
    }
}
