<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Invite;
use App\Models\RoleInvite;
use App\Models\Organisation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Providers\RouteServiceProvider;

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

        if (!$invite) {
            abort(403, "It looks like the invitation token is invalid. Please contact the person who invited you to the platform and ask for a new invitation.");
        }

        if ($invite->is_confirmed) {
            abort(403, "It looks like this invitation has already been used. If you have created an account, please <a href='" . url('login') . "'>login</a>. If you need a new invitation, please contact the person who sent you the invitation email.");
        }


        $messageStub = $invite->team ? $invite->team->name : config('app.name');

        $inviteMessage = "You have been invited to join the " . $messageStub . ".";

        return view('auth.register', compact('invite', 'inviteMessage'));
    }


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

        // Note: model_has_roles record has been created at this point.
        // It is good if we can pass in orgnisation_id for creating model_has_roles record.
        // But it may take a long time to hack how does Spatie permissions pacakge create model_has_roles record.
        //
        // A quick workaround is to find the newly created model_has_roles record (it always has organisation_id as "1" for default value),
        // then update organisation_id for institutional roles.
        // For global roles, we will need to re-generate model_has_roles records for all organisations

        // Get the mininum role_id of user, suppose user can have one role for any organisation currently
        $roles = DB::select('SELECT MIN(role_id) AS min_role_id FROM model_has_roles WHERE model_id = ' . $user->id);
        $minRoleId = $roles[0]->min_role_id;

        if ($minRoleId == 1 || $minRoleId == 2) {
            // Remove existing model_has_roles belong to user
            $deleteSql = "DELETE FROM model_has_roles WHERE model_id = " . $user->id;
            DB::statement($deleteSql);

            // find all organisations
            $allOrganisations = Organisation::all();


            foreach ($allOrganisations as $organisation) {
                // add model_has_roles records for all organisations
                $insertSql = "INSERT INTO model_has_roles VALUES (" . $minRoleId . ", 'App\\\Models\\\User', " . $user->id . ", " . $organisation->id . ")";
                DB::statement($insertSql);

                // add organisation_member records for all organisations
                $insertSql = "INSERT INTO organisation_members (user_id, organisation_id, role) VALUES (" . $user->id . ", " . $organisation->id . ", 'editor')";
                DB::statement($insertSql);
            }
        } else {
            // update organisation_id to model_has_roles record
            $updateSql = "UPDATE model_has_roles SET organisation_id = " . $request->organisation_id . " WHERE model_id = " . $user->id;
            DB::statement($updateSql);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
