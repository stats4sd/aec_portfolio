<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SessionController extends Controller
{

    // to store initiative page settings into session variables
    public function store(Request $request) {
        Session::put('sortBy', $request->request->get('sortBy'));
        Session::put('sortDir', $request->request->get('sortDir'));
        Session::put('redlineStatusFilterValue', $request->request->get('redlineStatusFilterValue'));
        Session::put('principleStatusFilterValue', $request->request->get('principleStatusFilterValue'));
        Session::put('portfolioFilter', $request->request->get('portfolioFilter'));
        Session::put('searchString', $request->request->get('searchString'));
    }

    // to reset session variables for all initiative page settings, then go to initiative page
    public function reset(Request $request) {
        Session::put('sortBy', 'name');
        Session::put('sortDir', '1');
        Session::forget('redlineStatusFilterLabel');
        Session::forget('redlineStatusFilterValue');
        Session::forget('principleStatusFilterLabel');
        Session::forget('principleStatusFilterValue');
        Session::forget('portfolioFilter');
        Session::forget('searchString');

        return redirect('/admin/project');
    }

}
