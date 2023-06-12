<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

    // original program source code in custom.php
    // public function check()
    // {
    //     if (Auth::user()->organisations()->count() > 1 || Auth::user()->hasRole('Site Admin')) {
    //         return redirect(backpack_url('organisation'));
    //     }

    //     if (Auth::user()->organisations()->count() === 1) {
    //         return redirect(backpack_url('organisation/' . Auth::user()->organisations->first()->id . '/show'));
    //     }

    //     abort(403, "It looks like you are not a member of any institution, and are not a site admin. If you think this is incorrect, please contact support@stats4sd.org");
    // }

    public function check()
    {

        // user can select institution, redirect to select institution page
        if (Auth::user()->can("select institution")) {
            logger("select institution - YES");
            return redirect(backpack_url('select_organisation'));
        } else {
            logger("select institution - NO");
        }

        // user cannot select institution, user belongs to one institution only.
        // TODO: there is no role_has_permission record for permission 38, but it is strange that hasAnyPermission() returns true...
        if (Auth::user()->can("auto set default institution")) {
            logger("auto set default institution - YES");

            // get institution Id, then simulate the user has selected an institution.
            $organisationId = Auth::user()->organisations()->first()->id;

            return redirect(backpack_url('selected_organisation') . '?organisationId=' . $organisationId);
        } else {
            logger("auto set default institution - NO");
        }

        // for a role without above permisssions, not sure why it never reach this point
        logger(403);
        abort(403, "It looks like you do not have permission to access this web site. If you think this is incorrect, please contact support@stats4sd.org");
    }

}
