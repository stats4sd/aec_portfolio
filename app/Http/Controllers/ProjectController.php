<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Organisation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::with([
            'portfolio' => [
                'organisation'
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


        // only enable features if the agreement is signed;
        if ($org->aggreement_signed_at) {

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
            }

            if (Auth::user()->can('view projects')) {
                $enableShowButton = true;
            }

            if (Auth::user()->can('assess project')) {
                $enableAssessButton = true;
            }

        }

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

}
