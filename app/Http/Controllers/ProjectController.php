<?php

namespace App\Http\Controllers;

use App\Models\HelpTextEntry;
use App\Models\Project;
use App\Models\Portfolio;
use Illuminate\Support\Str;
use App\Models\Organisation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        // get project id from session for redirection
        $projectId = Session::get('projectId');

        ray('project id: ' . $projectId);

        // redirect if project id existed in session before
        if ($projectId) {

            ray('redirecting');
            Session::put('projectId', '');
            return redirect(url('admin/project#' . $projectId));
        }

        $expandedProjects = Session::get('expandProject');


        // handle portfolio url - override existing session
        if ($request->has('portfolioFilter')) {
            Session::put('portfolioFilter', $request->get('portfolioFilter'));
        }

        $projects = Project::with([
            'portfolio' => [
                'organisation',
            ],
            'assessments' => [
                'principles',
                'failingRedlines',
                'additionalCriteria',
            ],
        ])
            // by default, initaitives are sorted by name in ascending order
            ->orderBy('name', 'asc')
            ->get()
            ->map(fn(Project $project) => $project->append('latest_assessment'));

        $org = Organisation::find(Session::get('selectedOrganisationId'));

        $hasAdditionalAssessment = $org->additionalCriteria->count() > 0 && $org->has_additional_criteria;

        $showAddButton = false;
        $showImportButton = false;
        $showExportButton = false;


        $enableEditButton = false;
        $enableShowButton = false;
        $enableAssessButton = false;
        $enableDeleteButton = false;
        $enableReassessButton = false;


        // only enable features if the agreement is signed;
        if ($org->agreement_signed_at) {

            if (Auth::user()->can('maintain projects')) {
                $showAddButton = true;
                $showImportButton = true;
            }

            if (Auth::user()->can('download project-level data')) {
                $showExportButton = true;
            }

            if (Auth::user()->can('maintain projects')) {
                $enableEditButton = true;
                $enableDeleteButton = true;
                $enableReassessButton = true;
            }

            if (Auth::user()->can('view projects')) {
                $enableShowButton = true;
            }

            if (Auth::user()->can('assess project')) {
                $enableAssessButton = true;
            }
        }

        // get settings from session
        $sortBy = Session::get('sortBy') ?? 'name';
        $sortDir = Session::get('sortDir') ?? 1;
        $redlineStatusFilterValue = Session::get('redlineStatusFilterValue') ?? '';
        $principleStatusFilterValue = Session::get('principleStatusFilterValue') ?? '';
        $portfolioFilter = Session::get('portfolioFilter') ?? '';
        $searchString = Session::get('searchString') ?? '';

        // get previously editing project id from session
        $projectId = Session::get('projectId') ?? '';

        $settings = [
            'sortBy' => $sortBy,
            'sortDir' => $sortDir,
            'redlineStatusFilterValue' => $redlineStatusFilterValue,
            'principleStatusFilterValue' => $principleStatusFilterValue,
            'portfolioFilter' => $portfolioFilter,
            'searchString' => $searchString,
        ];

        // get help text for cards. We do this manually here so that we don't need to send ajax requests from every card individually.
        $statusHelpText = HelpTextEntry::firstWhere('location', 'Initiatives - statuses');
        $scoreHelpText = HelpTextEntry::firstWhere('location', 'Initiatives - score');

        return view('projects.index', [
            'organisation' => $org,
            'projects' => $projects,
            'has_additional_assessment' => $hasAdditionalAssessment,
            'show_add_button' => $showAddButton,
            'show_import_button' => $showImportButton,
            'show_export_button' => $showExportButton,
            'enable_edit_button' => $enableEditButton,
            'enable_show_button' => $enableShowButton,
            'enable_assess_button' => $enableAssessButton,
            'enable_delete_button' => $enableDeleteButton,
            'enable_reassess_button' => $enableReassessButton,
            'settings' => $settings,
            'statusHelpText' => $statusHelpText,
            'scoreHelpText' => $scoreHelpText,
            'expandedProjects' => $expandedProjects != null ? $expandedProjects : '',
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        //
    }

    // store project Id into session
    public function storeProjectIdInSession()
    {
        $projectId = request('projectId');
        $expanded = request('expanded');

        // use for expanding a project

        if ($expanded) {
            Session::put('expandProject.' . $projectId, $projectId);
        } else {
            Session::pull('expandProject.' . $projectId);
        }

        return Session::get('expandProject');
    }
}
