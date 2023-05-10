<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\OrganisationMember;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class MyRoleController extends Controller
{

    public function show(Request $request)
    {
        // get organisation object from session
        $organisation = Session::get('selectedOrganisation');

        return view('my-role.show', [
            'organisation' => $organisation,
        ]);
    }

    public function requestToLeave(Request $request)
    {
        // get organisation object from session
        $organisation = Session::get('selectedOrganisation');

        return view('my-role.request-to-leave', [
            'organisation' => $organisation,
        ]);
    }

    public function confirmToLeave(Request $request)
    {
        // get organisation object from session
        $organisation = Session::get('selectedOrganisation');

        // remove the linkage between user account and institution
        OrganisationMember::where('user_id', auth()->user()->id)->where('organisation_id', $organisation->id)->delete();

        // check whether user also request to remove personal data
        if ($request['remove_personal_data'] == 'on') {
            // update user name, email address of user account
            $user = User::find(auth()->user()->id);
            $user->name = $user->id . '_removed_name';
            $user->email = $user->id . '_removed_email';
            $user->save();
        }

        // logout user 
        Auth::logout();

        // redirect user to login page
        return redirect('/login');
    }

    public function requestToRemoveEverything(Request $request)
    {
        logger("MyRoleController.requestToRemoveEverything()");

        // get organisation object from session
        $organisation = Session::get('selectedOrganisation');

        return view('my-role.request-to-remove-everything', [
            'organisation' => $organisation,
        ]);
    }


    public function confirmToRemoveEverything(Request $request)
    {
        logger("MyRoleController.confirmToRemoveEverything()");

    }    

}
