<?php

namespace App\Http\Controllers;

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
        // extract portfolio from GET parameter
        $paraPortfolio = $request->input('portfolio');

        // My Institution > click "SHOW INITIATIVES" button of a portfolio
        // The corresponding portfolio is considered as user selected portfolio.
        // When user clicks "Initiatives", initiative page will only show all initiatives of the selected portfolio.
        // User can reset the selected portfolio by clicking "My Institution", as this action means user wants to 
        // select another portfolio.
        // 
        // By the way, I would expect this handling may create user confusion.
        // Some users may select a portfolio, then keep clicking "Initiatives" and asking where are my other initiatives...

        if ($paraPortfolio == null) {
            // keep log message for future debugging when necessary
            // logger("URL does not have portfolio in GET parameter. Need to check whether user has selected a portfolio");

            $selectedPortfolio = Session::get('selectedPortfolio');
            // logger('selectedPortfolio: ' . Session::get('selectedPortfolio'));

            if ($selectedPortfolio != '') {
                // Note:
                //
                // 1. It may be a rare case that, user A selected portfolio X, stored it in session.
                // Then user B removed portfolio X, so that portfolio X is not accessible anymore.
                // In this case, portfolio filter will be filled with portfolio name, and there is no initiatives showed
                // 
                // 2. This is possible to check whether portfolio existence here. 
                // But it becomes complicated when different institution can have the same portfolio name.
                // Let's keep it simple here. As the worst case to search a non-exist portfolio is no initiative showed only

                // logger("User has selected portfolio: " . $selectedPortfolio);
                return redirect('/admin/project?portfolio=' . $selectedPortfolio);

            // } else {
            //     logger("User has not selected any portfolio, show all initiatives");
            }

        } else {
            // logger("URL has portfolio in GET parameter, store it in session. Show all initiatives of selected portfolio");
            
            // store user selected portfolio in session
            Session::put('selectedPortfolio', $paraPortfolio);
            // logger('selectedPortfolio: ' . Session::get('selectedPortfolio'));
        }


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

        $hasAdditionalAssessment = $org->additionalCriteria->count() > 0;

        $showAddButton = false;
        $showImportButton = false;
        $showExportButton = false;

        if (Auth::user()->can('maintain projects')) {
            $showAddButton = true;
            $showImportButton = true;
        }

        if (Auth::user()->can('download project-level data')) {
            $showExportButton = true;
        }

        $enableEditButton = false;
        $enableShowButton = false;
        $enableAssessButton = false;

        if (Auth::user()->can('maintain projects')) {
            $enableEditButton = true;
        }

        if (Auth::user()->can('view projects')) {
            $enableShowButton = true;
        }

        if (Auth::user()->can('assess project')) {
            $enableAssessButton = true;
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        //
    }
}
