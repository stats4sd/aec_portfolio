<?php

namespace App\Http\Controllers\Admin;

use App\Models\Organisation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SelectOrganisationController extends Controller
{

    // show all organisations for user selection
    public function show()
    {
        $organisations = Organisation::orderBy('name')->get();

        return view('organisations.select', ['organisations' => $organisations]);
    }

    // store user selected institution in session
    public function selected(Request $request)
    {
        // get parameter from form request
        $selectedOrganisationId = $request->input('organisationId');

        // get organisation
        $selectedOrganisation = Organisation::find($selectedOrganisationId);

        // store user selected organisation in session
        Session::put('selectedOrganisationId', $selectedOrganisationId);
        Session::put('selectedOrganisation', $selectedOrganisation);

        // redirect to a page that simply showed the selected organisation
        return view('organisations.selected', [
            'organisation' => $selectedOrganisation,
        ]);

    }

}
