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
        $paramPortfolioFilter = $request->input('portfolioFilter');
        $paramProcessed = $request->input('processed');

        if ($paramProcessed == null) {
            logger("processed is null. Now we proceed to final processing");

            if ($paramPortfolioFilter != null) {
                logger('$paramPortfolioFilter is not null');
                logger("request come from My Institution > portfolio list > SHOW INITIATIVE button");
                logger("reset all settings, store the selected portfolio in session, show all initiatives of this portfolio");

                Session::put('sortBy', 'name');
                Session::put('sortDir', '1');
                Session::put('redlineStatusFilterLabel', '');
                Session::put('redlineStatusFilterValue', '');
                Session::put('principleStatusFilterLabel', '');
                Session::put('principleStatusFilterValue', '');
                Session::put('portfolioFilter', $paramPortfolioFilter);
                Session::put('searchString', '');
            } else {
                logger("user clicks Initiatives to enter initiative page");
                logger("show initiatives with all settings in session");
            }

            logger("get settings from session");

            $sortBy = Session::get('sortBy') == null ? "name" : Session::get('sortBy');
            $sortDir = Session::get('sortDir') == null ? "1" : Session::get('sortDir');
            $portfolioFilter = Session::get('portfolioFilter');

            $queryString = '?';
            $queryString = $queryString . 'sortBy=' . $sortBy;
            $queryString = $queryString . '&sortDir=' . $sortDir;
            $queryString = $queryString . '&redlineStatusFilterLabel=' . Session::get('redlineStatusFilterLabel');
            $queryString = $queryString . '&redlineStatusFilterValue=' . Session::get('redlineStatusFilterValue');
            $queryString = $queryString . '&principleStatusFilterLabel=' . Session::get('principleStatusFilterLabel');
            $queryString = $queryString . '&principleStatusFilterValue=' . Session::get('principleStatusFilterValue');
            $queryString = $queryString . '&portfolioFilter=' . $portfolioFilter;
            $queryString = $queryString . '&searchString=' . Session::get('searchString');
            $queryString = $queryString . '&processed=1';

            logger('$queryString: ' . $queryString);

            return redirect('/admin/project' . $queryString);
            
        } else {
            logger("processed is not null. User has been redirected with final processing, do nothing");
        }


        ////////////////////
        

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

}
