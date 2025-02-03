<?php

namespace App\Http\Middleware;

use App\Models\Organisation;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganisationIsSelected
{
    public function handle(Request $request, Closure $next): Response
    {

        if ($request->session()->has('selectedOrganisationId')) {
            return $next($request);
        }

        // if user has only 1 institution, select it and move on.
        if (Organisation::count() === 1) {
            Session::put('selectedOrganisationId', Organisation::first()->id);

            // redirect to restart the entire request / middleware stack, so that the initial attempt to set the team for role/permission assignment can be done
            return redirect($request->url());
        }

        // For users with multiple institutions redirect them to select an organisation.
        Session::flash('redirect', $request->fullUrl());

        return redirect(backpack_url('selected_organisation'));
    }
}
