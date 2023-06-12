<?php

namespace App\Http\Controllers;

use App\Mail\DataRemovalRequested;
use App\Models\Organisation;
use App\Models\OrganisationMember;
use App\Models\RemovalRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class MyRoleController extends Controller
{

    public function show(Request $request)
    {
        // get organisation object from session
        $organisation = Organisation::find(Session::get('selectedOrganisation'));

        return view('my-role.show', [
            'organisation' => $organisation,
        ]);
    }

    public function requestToLeave(Request $request)
    {
        // get organisation object from session
        $organisation = Organisation::find(Session::get('selectedOrganisation'));

        return view('my-role.request-to-leave', [
            'organisation' => $organisation,
        ]);
    }

    public function confirmToLeave(Request $request)
    {
        // get organisation object from session
        $organisation = Organisation::find(Session::get('selectedOrganisation'));

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
        // get organisation object from session
        $organisation = Organisation::find(Session::get('selectedOrganisation'));

        return view('my-role.request-to-remove-everything', [
            'organisation' => $organisation,
        ]);
    }


    public function confirmToRemoveEverything(Request $request)
    {
        // get organisation object from session
        $organisation = Organisation::find(Session::get('selectedOrganisation'));

        $removalRequest = RemovalRequest::create([
            'organisation_id' => $organisation->id,
            'requester_id' => Auth::user()->id,
            'status' => 'REQUESTED',
            'requested_at' => Carbon::now(),
        ]);

        // send email to requester and site admin
        $toRecipients = [$removalRequest->requester->email, config('mail.data_removal_alert_recipients')];

        Mail::to($toRecipients)->queue(new DataRemovalRequested($removalRequest));

        // redirect user to request submitted page
        return view('my-role.request-submitted', [
            'organisation' => $organisation,
        ]);

    }

}
