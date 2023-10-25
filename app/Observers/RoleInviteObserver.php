<?php

namespace App\Observers;

use App\Models\RoleInvite;
use App\Mail\RoleInviteMember;
use Illuminate\Support\Facades\Mail;

class RoleInviteObserver
{

    public function created(RoleInvite $invite)
    {
        Mail::to($invite->email)->send(new RoleInviteMember($invite));
    }
}
