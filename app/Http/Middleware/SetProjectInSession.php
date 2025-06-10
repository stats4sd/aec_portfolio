<?php

namespace App\Http\Middleware;

use App\Models\Assessment;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class SetProjectInSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        // if route is a project route:
        if (Str::contains($request->url(), 'project/')) {

            // ray('project route detected');

            // if the route params has an ID or a 'project'
            $parameters = $request->route()->parameters();

            if (isset($parameters['project'])) {
                Session::put('projectId', $parameters['project']);
            }

            if (isset($parameters['id'])) {
                Session::put('projectId', $parameters['id']);
            }
        }

        // if route is an assessment route:
        if (Str::contains($request->url(), 'assessment/')) {
            $parameters = $request->route()->parameters();

            if (isset($parameters['assessment'])) {

                $project = $parameters['assessment']->project;

                Session::put('projectId', $project->id);
            }

            if (isset($parameters['id'])) {

                $project = Assessment::find($parameters['id'])->project;

                Session::put('projectId', $project->id);
            }
        }


        return $next($request);
    }
}
