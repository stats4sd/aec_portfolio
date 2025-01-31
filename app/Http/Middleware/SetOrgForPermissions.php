<?php

namespace App\Http\Middleware;

use App\Models\Organisation;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetOrgForPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        // temporarily set permissions ID to let admins and site managers access the site to choose an organisation
        if (! getPermissionsTeamId()) {
            setPermissionsTeamId(Organisation::withoutGLobalScope('owned')->first()->id);
        }

        return $next($request);
    }
}
