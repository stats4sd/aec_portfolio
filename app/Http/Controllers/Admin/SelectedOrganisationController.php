<?php

namespace App\Http\Controllers\Admin;

use App\Models\Organisation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SelectedOrganisationController extends Controller
{

    //
    public function create()
    {
        $organisations = Organisation::orderBy('name')->get();

        return view('organisations.select', ['organisations' => $organisations]);
    }

    // store user selected institution in session
    public function store(Request $request)
    {

        $request->validate([
            'organisationId' => 'exists:organisations:id',
        ]);

        // store user selected organisation in session
        Session::put('selectedOrganisationId', $request->input('organisationId'));

        // redirect to a page that simply showed the selected organisation
        return view('organisations.selected', [
            'organisation' => Organisation::find($request->input('organisationId')),
        ]);

    }

}
