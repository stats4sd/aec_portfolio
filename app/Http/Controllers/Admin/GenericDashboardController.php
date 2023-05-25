<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class GenericDashboardController extends Controller
{

    public function show(Request $request)
    {
        // get organisation object from session
        $organisation = Session::get('selectedOrganisation');

        $level = $request->level;

        // TODO: error handling if there is no organisation selected yet

        return view('generic-dashboard.new-dashboard', [
            'organisation' => $organisation,
            'level' => $level,
        ]);
    }

}
