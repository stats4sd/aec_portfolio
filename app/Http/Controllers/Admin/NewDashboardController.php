<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class NewDashboardController extends Controller
{

    public function show()
    {
        // get organisation object from session
        $organisation = Session::get('selectedOrganisation');

        // TODO: error handling if there is no organisation selected yet

        return view('organisations.new-dashboard', ['organisation' => $organisation]);
    }

}
