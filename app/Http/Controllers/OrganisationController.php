<?php

namespace App\Http\Controllers;

use App\Enums\AssessmentStatus;
use App\Exports\Assessment\AssessmentExportWorkbook;
use App\Exports\MergedExport;
use App\Jobs\ExportOrgData;
use App\Models\Organisation;
use App\Models\Principle;
use App\Models\Project;
use App\Models\RedLine;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use Prologue\Alerts\Facades\Alert;

class OrganisationController extends Controller
{

    public function publicIndex()
    {
        return Organisation::withCount(['projects'])->get();
    }

    public function portfolio(Organisation $organisation)
    {
        // hotfix for no projects
        if ($organisation->projects()->count() === 0) {
            Alert::add('info', 'This institution has no projects. Please add at least one project before reviewing the portfolio page')->flash();
            return back();
        }


        // get data for spider chart
        $principles = Principle::select(['name', 'id'])->get();

        // Question: This function is to be used for the current dashboard page with spider chart.
        // As we will have a Vue component based dashboard. it seems not to be used anymore in future.
        // Can we leave "assessment_status" code as is in this function?

        // overall stats
        $assessedProjects = $organisation
            ->projects
            ->where('assessment_status', '=', AssessmentStatus::Complete);


        $overallScore = round($assessedProjects->avg('overall_score'), 2);
        $count = $assessedProjects->count();
        $totalBudget = $assessedProjects->sum('budget');
        $agroecologyBudget = $totalBudget * ($overallScore / 100);

        $ratings = $organisation->projects()
            ->where('assessment_status', "=", AssessmentStatus::Complete)
            ->with('principleProjects.principle')
            ->get()
            ->map(function ($project) {
                $output = collect([]);
                foreach ($project->principleProjects as $principleProject) {
                    $output[$principleProject->principle->name] = $principleProject->rating;
                }
                return $output;
            });

        $avgs = collect([]);
        foreach ($principles as $principle) {
            $avgs[$principle->name] = round($ratings->avg($principle->name), 2);
        }

        $spiderData = $avgs->map(function ($avg, $key) {
            return [
                'axis' => $key,
                'value' => $avg,
            ];
        });

        // redlines
        $redlines = RedLine::select('id', 'name')
            ->with('failingProjects', function ($query) use ($organisation) {
                $query->where('organisation_id', $organisation->id);
            })->get();

        // comparative
        $allRatings = Project::withoutGlobalScope('organisation')
            ->where('assessment_status', "=", AssessmentStatus::Complete)
            ->with('principleProjects.principle')
            ->get()
            ->map(function ($project) {
                $output = collect([]);
                foreach ($project->principleProjects as $principleProject) {
                    $output[$principleProject->principle->name] = $principleProject->rating;
                }
                return $output;
            });
        $allAvgs = collect([]);
        $yourPortfolio = collect([]);
        $allPortfolio = collect([]);
        $naPrinciples = collect([]);

        $passedProjects = $assessedProjects->filter(fn($project) => $project->assessments->last()->overall_score > 0);

        $ratingsForPassedProjects = $passedProjects
            ->map(function ($project) {
                $output = collect([]);
                foreach ($project->principleProjects as $principleProject) {
                    $output[$principleProject->principle->name] = $principleProject->rating;
                }
                return $output;
            });


        foreach ($principles as $principle) {
            $allAvgs[$principle->name] = round($allRatings->avg($principle->name), 2);

            $yourPortfolioPrinciple = collect([]);
            $allPortfolioPrinciple = collect([]);
            $naPrinciplesTemp = 0;

            foreach ($ratings as $rating) {
                if ($rating[$principle->name]) {
                    $yourPortfolioPrinciple[] = $rating[$principle->name];

                }
            }
            foreach ($allRatings as $rating) {
                if ($rating[$principle->name]) {
                    $allPortfolioPrinciple[] = $rating[$principle->name];

                }
            }

            foreach ($ratingsForPassedProjects as $rating) {
                if (!$rating[$principle->name]) {
                    $naPrinciplesTemp++;
                }
            }

            $yourPortfolio[$principle->name] = $yourPortfolioPrinciple;
            $allPortfolio[$principle->name] = $allPortfolioPrinciple;
            $naPrinciples[$principle->name] = $naPrinciplesTemp;

        }

        // TEMP HACK
        $currency = $organisation->projects->first()->currency;


        return view('organisations.portfolio', [
            'organisation' => $organisation,
            'assessedProjects' => $assessedProjects,
            'ratings' => $ratings,
            'avgs' => $avgs,
            'spiderData' => $spiderData,
            'overallScore' => $overallScore,
            'count' => $count,
            'totalBudget' => $totalBudget,
            'agroecologyBudget' => $agroecologyBudget,
            'redlines' => $redlines,
            'principles' => $principles,
            'allAvgs' => $allAvgs,
            'yourPortfolio' => $yourPortfolio,
            'allPortfolio' => $allPortfolio,
            'naPrinciples' => $naPrinciples,
            'passedProjects' => $passedProjects,
            'currency' => $currency,
        ]);
    }


    public function export()
    {
        $organisation = Organisation::find(Session::get('selectedOrganisationId'));

        return Excel::download(new AssessmentExportWorkbook($organisation), "AEC - Data Export - " . $organisation->name . "-" .
            Carbon::now()->toDateTimeString() . ".xlsx");
    }

    public function exportAll()
    {
        $this->authorize('viewAny', Organisation::class);

        foreach(Organisation::all() as $organisation) {

            $timestamp = Carbon::now()->toDateTimeString();

            ExportOrgData::dispatch($organisation, $timestamp);

        }

        return 'done - queued';
    }

    public function mergeAll()
    {
        return Excel::download(new MergedExport(), 'test.xlsx');
    }


}
