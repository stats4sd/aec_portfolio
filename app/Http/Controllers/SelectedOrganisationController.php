<?php

namespace App\Http\Controllers;

use App\Models\Organisation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SelectedOrganisationController extends Controller
{

    //
    public function create()
    {
        $organisations = Organisation::orderBy('name')->get();

        // if only one organisation is available to the user, automatically select it (same as the middleware)
        if ($organisations->count() === 1) {
            $orgId = $organisations->first()->id;
            Session::put('selectedOrganisationId', $orgId);

            $redirect = backpack_url('organisation/show');

            return redirect($redirect);
        }

        return view('organisations.select', ['organisations' => $organisations]);
    }

    // store user selected institution in session
    public function store(Request $request)
    {
        $request->validate([
            'organisationId' => 'exists:organisations,id',
        ]);

        // store user selected organisation in session
        Session::put('selectedOrganisationId', $request->input('organisationId'));

        // redirect to a page that simply showed the selected organisation
        $redirect = $request->input('redirect') ?? backpack_url('organisation/show');

        return redirect($redirect);

    }

    public function show()
    {
        return redirect(backpack_url('organisation/show'));
    }

}
