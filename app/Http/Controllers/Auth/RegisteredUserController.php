<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Invite;
use App\Models\RoleInvite;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisteredUserController extends Controller
{

    public function create()
    {
        $invite = null;
        $inviteMessage = null;
        if (request()->has('token')) {
            $invite = Invite::withoutGlobalScope('unconfirmed')
                ->where('token', '=', request()->token)
                ->first();
        }
        if (!$invite) {
            $invite = RoleInvite::withoutGlobalScope('unconfirmed')
                ->where('token', '=', request()->token)
                ->first();
        }

        if(!$invite) {
            abort(403, "It looks like the invitation token is invalid. Please contact the person who invited you to the platform and ask for a new invitation.");
        }

        if($invite->is_confirmed) {
           abort(403, "It looks like this invitation has already been used. If you have created an account, please <a href='" .url('login') . "'>login</a>. If you need a new invitation, please contact the person who sent you the invitation email.");
        }


        $messageStub = $invite->team ? $invite->team->name : config('app.name');

        $inviteMessage = "You have been invited to join the " . $messageStub . ".";

        return view('auth.register', compact('invite', 'inviteMessage'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
