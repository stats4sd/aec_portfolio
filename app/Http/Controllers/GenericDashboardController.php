<?php

namespace App\Http\Controllers;

use App\Enums\AssessmentStatus;
use App\Models\Assessment;
use App\Models\Country;
use App\Models\InitiativeCategory;
use App\Models\Organisation;
use App\Models\Portfolio;
use App\Models\Principle;
use App\Models\PrincipleAssessment;
use App\Models\Project;
use App\Models\ProjectRegion;
use App\Models\Region;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class GenericDashboardController extends Controller
{

    // TEMP
    // TODO: OLD ONE TO BE REMOVED
    public function new(Request $request)
    {
        $data = $this->getData($request);

        $data['portfolios'] = $data['organisation']['portfolios'];

        return view('generic-dashboard.new-dashboard', $data);
    }

    public function show(Request $request)
    {
        $data = $this->getData($request);
        return view('generic-dashboard.dashboard', $data);
    }

    public function getData(Request $request)
    {

        $org = Organisation::with([
            'portfolios.projects' => [
                'regions',
                'countries',
            ]
        ])->find(Session::get('selectedOrganisationId'));

        $regions = $org->portfolios
            ->map(
                fn (Portfolio $portfolio): Collection => $portfolio
                    ->projects
                    ->map(
                        fn (Project $project): Collection => $project
                            ->regions
                    )
            )
            ->flatten()
            ->unique('id')
            ->values();

        $countries = $org->portfolios
            ->map(
                fn (Portfolio $portfolio): Collection => $portfolio
                    ->projects
                    ->map(
                        fn (Project $project): Collection => $project
                            ->countries
                    )
            )
            ->flatten()
            ->unique()
            ->values();

        $categories = $org->portfolios
            ->map(
                fn (Portfolio $portfolio): Collection => $portfolio
                    ->projects
                    ->map(
                        fn (Project $project): ?InitiativeCategory => $project
                            ->initiativeCategory
                    )
            )
            ->flatten()
            ->unique()
            ->values();

        return [
            'organisation' => $org,
            'regions' => $regions,
            'countries' => $countries,
            'categories' => $categories,
        ];
    }

    public function enquire(Request $request)
    {
        // get system time as unique dashboard Id
        $current_timestamp = Carbon::now()->timestamp;

        // variables for stored procedure input parameters
        $dashboardYoursId = $current_timestamp;
        $dashboardOthersId = $current_timestamp + 1;
        $organisationId = $request['organisation_id'];


        // nullable items are cast to "null" string for inclusion into SQL query
        $portfolioId = $request['portfolio']['id'] ?? 'null';
        $regionIds = $request['regions'] ? "'" . collect($request['regions'])->pluck('id')->join(', ') . "'" : 'null';
        $countryIds = $request['countries'] ? "'" . collect($request['countries'])->pluck('id')->join(', ') . "'" : 'null';
        $categoryIds = $request['categories'] ? "'" . collect($request['categories'])->pluck('id')->join(', ') . "'" : 'null';
        $projectStartFrom = $request['startDate'] ?? 'null';
        $projectStartTo = $request['endDate'] ?? 'null';
        $budgetFrom = $request['minBudget'] ?? 'null';
        $budgetTo = $request['maxBudget'] ?? 'null';

        // sort principles by principle number by default
        $sortBy = $request['sortBy'] ?? '3';

        // constrcuct dynamic SQL
        $sql = "CALL generate_dashboard_summary(
            {$dashboardYoursId},
            {$dashboardOthersId},
            {$organisationId},
            {$portfolioId},
            {$regionIds},
            {$countryIds},
            {$categoryIds},
            {$projectStartFrom},
            {$projectStartTo},
            {$budgetFrom},
            {$budgetTo},
            @status,
            @message,
            @totalCount,
            @totalBudget,
            @tooFewOtherProjects,
            @statusSummary,
            @redlinesSummary,
            @yoursPrinciplesSummary,
            @othersPrinciplesSummary)
            ";

        // call stored procedure to get dashboard summary data
        // DB::select("CALL generate_dashboard_summary(998, 999, 9, 20, null, null, null, null, null, null, @status, @message, @statusSummary, @redlinesSummary, @yoursPrinciplesSummary, @othersPrinciplesSummary)");
        DB::select($sql);

        $results = DB::select('select
        @status as status,
        @message as message,
        @totalCount as totalCount,
        @totalBudget as totalBudget,
        @tooFewOtherProjects as tooFewOtherProjects,
        @statusSummary as statusSummary,
        @redlinesSummary as redlinesSummary,
        @yoursPrinciplesSummary as yoursPrinciplesSummary,
        @othersPrinciplesSummary as othersPrinciplesSummary');

        $status = $results[0]->status;
        $message = $results[0]->message;


        // error handling if status is not 0
        if ($status !== 0) {
            $jsonRes = [];

            $jsonRes['status'] = $status;
            $jsonRes['message'] = $message;
            $jsonRes['statusSummary'] = null;
            $jsonRes['redlinesSummary'] = null;
            $jsonRes['yoursPrinciplesSummarySorted'] = null;
            $jsonRes['othersPrinciplesSummarySorted'] = null;
            $jsonRes['dashboardYoursId'] = $dashboardYoursId;
            $jsonRes['tooFewOtherProjects'] = $results[0]->tooFewOtherProjects;

            return $jsonRes;
        }

        // convert string to JSON
        $statusSummary = json_decode($results[0]->statusSummary, true);
        $redlinesSummary = json_decode($results[0]->redlinesSummary, true);

        $yoursPrinciplesSummary = json_decode($results[0]->yoursPrinciplesSummary, true);
        $othersPrinciplesSummary = json_decode($results[0]->othersPrinciplesSummary, true);

        if ($results[0]->tooFewOtherProjects === 1) {
            $othersPrinciplesSummary = null;
        }

        // prepare principles summary with sorting preference
        $yoursPrinciplesSummarySorted = collect($yoursPrinciplesSummary)->filter(fn ($summary) => $summary !== null)->toArray();
        $othersPrinciplesSummarySorted = [];


        // highest to lowest score (highest green)
        if ($sortBy == 1) {
            // sort by green, yellow, red

            // sort by green first
            usort($yoursPrinciplesSummarySorted, function ($a, $b) {
                return $a['green'] <= $b['green'];
            });

            // then sort by yellow
            usort($yoursPrinciplesSummarySorted, function ($a, $b) {
                if ($a['green'] != $b['green']) {
                    return false;
                } else {
                    return $a['yellow'] <= $b['yellow'];
                }
            });

            // finally sort by red
            usort($yoursPrinciplesSummarySorted, function ($a, $b) {
                if ($a['green'] != $b['green']) {
                    return false;
                } else {
                    if ($a['yellow'] != $b['yellow']) {
                        return false;
                    } else {
                        return $a['red'] <= $b['red'];
                    }
                }
            });

            // lowest to highest score (highest red)
        } else if ($sortBy == 2) {
            // sort by red, yellow, green

            // sort by red first
            usort($yoursPrinciplesSummarySorted, function ($a, $b) {
                return $a['red'] <= $b['red'];
            });

            // then sort by yellow
            usort($yoursPrinciplesSummarySorted, function ($a, $b) {
                if ($a['red'] != $b['red']) {
                    return false;
                } else {
                    return $a['yellow'] <= $b['yellow'];
                }
            });

            // finally sort by green
            usort($yoursPrinciplesSummarySorted, function ($a, $b) {
                if ($a['red'] != $b['red']) {
                    return false;
                } else {
                    if ($a['yellow'] != $b['yellow']) {
                        return false;
                    } else {
                        return $a['red'] <= $b['red'];
                    }
                }
            });

            // default (order by principle number)
        } else if ($sortBy == 3) {
            // the original returned principles summary is sorted by principle number already, no additional work required here

        }

        // copy others principle summary item one by one according to the ordering of yours principles summary
        foreach ($yoursPrinciplesSummarySorted as $yoursItem) {

            if ($othersPrinciplesSummary) {

                foreach ($othersPrinciplesSummary as $othersItem) {
                    if ($othersItem['id'] == $yoursItem['id']) {
                        array_push($othersPrinciplesSummarySorted, $othersItem);
                        break;
                    }
                }
            }
        }


        // add overall score from PHP side because this is already calculated on the Assessment Model.
        $allAssessments = Project::withoutGlobalScope('organisation')->with([
            'assessments' => ['principleAssessments.principle', 'principles', 'failingRedlines']
        ])
            // only initiatives that are fully assessed
            ->whereHas('assessments', function (Builder $query) {
                $query->where('redline_status', AssessmentStatus::Failed->value)
                    ->orWhere('principle_status', AssessmentStatus::Complete->value);
            })
            ->get()
            ->pluck('assessments')
            ->flatten();

        // find latest assessment id for all projects
        $latestAssessmentIds = Assessment::select(DB::raw('MAX(id) as id'))
            ->groupBy('project_id')
            ->get()
            ->pluck('id');

        // only keep latest assessment from all assessments, previously completed assessments will be removed
        $allAssessments = $allAssessments->whereIn('id', $latestAssessmentIds);

        $allAssessmentsYours = $allAssessments->whereIn(
            'project_id',
            DB::table('dashboard_project')
                ->select('project_id')
                ->where('dashboard_id', $dashboardYoursId)
                ->get()
                ->pluck('project_id')
                ->toArray()
        );

        $allAssessmentsOthers = $allAssessments->whereIn(
            'project_id',
            DB::table('dashboard_project')
                ->select('project_id')
                ->where('dashboard_id', $dashboardOthersId)
                ->get()
                ->pluck('project_id')
                ->toArray()
        );


        $noOfInitiativeCompletedAssessment = $allAssessmentsYours->count();


        // initialise variables
        $assessmentScore = 'N/A';
        $aeBudget = 'N/A';

        // error handling to avoid division by zero error
        if ($noOfInitiativeCompletedAssessment == 0) {

            $status = 2001;
            $message = 'There is no fully assessed initiative';
        } else {
            // calculate overall score and AE budget if number of fully assess initiative is bigger than zero
            $assessmentScore = $allAssessmentsYours->sum(fn (Assessment $assessment) => $assessment->overall_score)
                / $noOfInitiativeCompletedAssessment;

            $aeBudget = round($results[0]->totalBudget * ($assessmentScore / 100), 0);

            $assessmentScore = round($assessmentScore, 1);
        }

        // count nas for each principle
        $yourNas = $this->getNaCount($allAssessmentsYours);
        $otherNas = $this->getNaCount($allAssessmentsOthers);

        // construct JSON response
        $jsonRes = [];

        $jsonRes['status'] = $status;
        $jsonRes['message'] = $message;
        $jsonRes['totalCount'] = $results[0]->totalCount;
        $jsonRes['totalBudget'] = $results[0]->totalBudget;
        $jsonRes['assessmentScore'] = $assessmentScore;
        $jsonRes['aeBudget'] = $aeBudget;
        $jsonRes['tooFewOtherProjects'] = $results[0]->tooFewOtherProjects;
        $jsonRes['statusSummary'] = $statusSummary;
        $jsonRes['redlinesSummary'] = $redlinesSummary;
        $jsonRes['yoursPrinciplesSummarySorted'] = $yoursPrinciplesSummarySorted;
        $jsonRes['othersPrinciplesSummarySorted'] = $othersPrinciplesSummarySorted;

        $jsonRes['yourNas'] = $yourNas;
        $jsonRes['otherNas'] = $otherNas;

        // TEMP
        $jsonRes['dashboardYoursId'] = $dashboardYoursId;

        return $jsonRes;
    }

    /**
     * Function to take a set of assessments and return a collection of the total count of "is_na" for each principle.
     */
    public function getNaCount(Collection $allAssessments): Collection
    {
        return $allAssessments->map(function (Assessment $assessment) {
            return $assessment->principleAssessments->map(function (PrincipleAssessment $principleAssessment) {
                return [
                    'id' => $principleAssessment->principle->id,
                    'name' => $principleAssessment->principle->name,
                    'value' => $principleAssessment->is_na ? 1 : 0
                ];
            });
        })->reduce(function ($carry, $naList) {
            return $carry->map(function ($value, $index) use ($naList) {
                return [
                    'id' => $value['id'],
                    'name' => $value['name'],
                    'value' => $value['value'] + $naList[$index]['value']
                ];
            });
        }, Principle::all()->map(fn (Principle $principle) => [
            'id' => $principle->id,
            'name' => $principle->name,
            'value' => 0
        ]))
            // convert to % of initiative

            ->map(function ($item) use ($allAssessments) {

                if ($allAssessments->count() > 0) {
                    $item['value'] = ($item['value'] / $allAssessments->count()) * 100;
                }
                return $item;
            });
    }
}
