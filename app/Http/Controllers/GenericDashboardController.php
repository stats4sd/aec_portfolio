<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\CountryProject;
use App\Models\Organisation;
use App\Models\Portfolio;
use App\Models\ProjectRegion;
use App\Models\Region;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class GenericDashboardController extends Controller
{

    public function show(Request $request)
    {
        logger("GenericDashboardController.show()...");

        // get organisation object from session
        $organisation = Organisation::find(Session::get('selectedOrganisationId'));


        // find all portfolios belong to selected organisation
        $portfolios = Portfolio::where('organisation_id', $organisation->id)->orderBy('id')->get();

        // find all project Ids belongs to selected organisation
        $projectIds = $organisation->projects->pluck('id')->toArray();

        // find all regions with projects that belong to selected organisation
        $regionIds = ProjectRegion::whereIn('project_id', $projectIds)->get()->pluck('region_id')->toArray();
        $countryIds = CountryProject::whereIn('project_id', $projectIds)->get()->pluck('country_id')->toArray();

        // find all regions with projects that belong to selected organisation
        $regions = Region::whereIn('regions.id', $regionIds)
            ->with('countries', function(HasMany $query) use ($countryIds) {
                $query->whereIn('countries.id', $countryIds);
            })
            ->get();

        // find all coutnries with projects that belong to selected organisation
        $countrieIds = CountryProject::whereIn('project_id', $projectIds)->get()->pluck('country_id')->toArray();

        // find all countries with projects that belong to selected organisation
        $countries = Country::whereIn('id', $countrieIds)->get();

        return view('generic-dashboard.new-dashboard', [
            'organisation' => $organisation,
            'portfolios' => $portfolios,
            'regions' => $regions,
            'countries' => $countries,
        ]);
    }

    public function enquire(Request $request)
    {
        // logger("GenericDashboardController.enquire()");

        // logger($request);

        // get system time as unique dashboard Id
        $current_timestamp = Carbon::now()->timestamp;

        // variables for stored procedure input parameters
        $dashboardYoursId = $current_timestamp;
        $dashboardOthersId = $current_timestamp + 1;
        $organisationId = $request['organisation'];
        $portfolioId = $request['portfolio'];
        $regionId = $request['region'];
        $countryId = $request['country'];
        $projectStartFrom = $request['projectStartFrom'];
        $projectStartTo = $request['projectStartTo'];
        $budgetFrom = $request['budgetFrom'];
        $budgetTo = $request['budgetTo'];
        $chkRegion = $request['chkRegion'];
        $chkCountry = $request['chkCountry'];
        $chkProjectStart = $request['chkProjectStart'];
        $chkBudget = $request['chkBudget'];
        $sortBy = $request['sortBy'];


        logger($dashboardYoursId);
        logger($dashboardOthersId);
        logger($organisationId);
        logger($portfolioId);
        logger($regionId);
        logger($countryId);
        logger($projectStartFrom);
        logger($projectStartTo);
        logger($budgetFrom);
        logger($budgetTo);
        logger($chkRegion);
        logger($chkCountry);
        logger($chkProjectStart);
        logger($chkBudget);
        logger($sortBy);


        // constrcuct dynamic SQL
        $sql = '';
        $sql = $sql . 'CALL generate_dashboard_summary(';
        $sql = $sql . $dashboardYoursId . ', ';
        $sql = $sql . $dashboardOthersId . ', ';
        $sql = $sql . $organisationId . ', ';

        // portfolio Id 0 means all portfolios
        if ($portfolioId == '0') {
            $sql = $sql . 'null, ';
        } else {
            $sql = $sql . $portfolioId . ', ';
        }

        if ($chkRegion == '1') {
            $sql = $sql . $regionId . ', ';
        } else {
            $sql = $sql . 'null, ';
        }

        if ($chkCountry == '1') {
            $sql = $sql . $countryId . ', ';
        } else {
            $sql = $sql . 'null, ';
        }

        if ($chkProjectStart == '1') {
            $sql = $sql . $projectStartFrom . ', ';
            $sql = $sql . $projectStartTo . ', ';
        } else {
            $sql = $sql . 'null, ';
            $sql = $sql . 'null, ';
        }

        if ($chkBudget == '1') {
            $sql = $sql . $budgetFrom . ', ';
            $sql = $sql . $budgetTo . ', ';
        } else {
            $sql = $sql . 'null, ';
            $sql = $sql . 'null, ';
        }

        $sql = $sql . '@status, @message, @statusSummary, @redlinesSummary, @yoursPrinciplesSummary, @othersPrinciplesSummary)';

        // logger($sql);


        // call stored procedure to get dashboard summary data
        // DB::select("CALL generate_dashboard_summary(998, 999, 9, 20, null, null, null, null, null, null, @status, @message, @statusSummary, @redlinesSummary, @yoursPrinciplesSummary, @othersPrinciplesSummary)");
        DB::select($sql);

        $results = DB::select('select @status as status, @message as message, @statusSummary as statusSummary, @redlinesSummary as redlinesSummary, @yoursPrinciplesSummary as yoursPrinciplesSummary, @othersPrinciplesSummary as othersPrinciplesSummary');

        logger($results);

        $status = $results[0]->status;
        $message = $results[0]->message;


        // error handling if status is not 0
        if ($status != "0") {
            $jsonRes = [];

            $jsonRes['status'] = $status;
            $jsonRes['message'] = $message;
            $jsonRes['statusSummary'] = null;
            $jsonRes['redlinesSummary'] = null;
            $jsonRes['yoursPrinciplesSummarySorted'] = null;
            $jsonRes['othersPrinciplesSummarySorted'] = null;

            return $jsonRes;
        }


        // convert string to JSON
        $statusSummary = json_decode($results[0]->statusSummary, true);
        $redlinesSummary = json_decode($results[0]->redlinesSummary, true);
        $yoursPrinciplesSummary = json_decode($results[0]->yoursPrinciplesSummary, true);
        $othersPrinciplesSummary = json_decode($results[0]->othersPrinciplesSummary, true);


        // prepare principles summary with sorting preference
        $yoursPrinciplesSummarySorted = collect($yoursPrinciplesSummary)->filter(fn($summary) => $summary !== null)->toArray();
        $othersPrinciplesSummarySorted = [];




        // highest to lowest score (highest green)
        if ($sortBy == 1) {
            // sort by green, yellow, red

            // sort by green first
            usort($yoursPrinciplesSummarySorted, function($a, $b) {
                return $a['green'] <= $b['green'];
            });

            // then sort by yellow
            usort($yoursPrinciplesSummarySorted, function($a, $b) {
                if ($a['green'] != $b['green']) {
                    return false;
                } else {
                    return $a['yellow'] <= $b['yellow'];
                }
            });

            // finally sort by red
            usort($yoursPrinciplesSummarySorted, function($a, $b) {
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
            usort($yoursPrinciplesSummarySorted, function($a, $b) {
                return $a['red'] <= $b['red'];
            });

            // then sort by yellow
            usort($yoursPrinciplesSummarySorted, function($a, $b) {
                if ($a['red'] != $b['red']) {
                    return false;
                } else {
                    return $a['yellow'] <= $b['yellow'];
                }
            });

            // finally sort by green
            usort($yoursPrinciplesSummarySorted, function($a, $b) {
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
            foreach ($othersPrinciplesSummary as $othersItem) {
                if ($othersItem['id'] == $yoursItem['id']) {
                    array_push($othersPrinciplesSummarySorted, $othersItem);
                    break;
                }
            }
        }


        // construct JSON response
        $jsonRes = [];

        $jsonRes['status'] = $status;
        $jsonRes['message'] = $message;
        $jsonRes['statusSummary'] = $statusSummary;
        $jsonRes['redlinesSummary'] = $redlinesSummary;
        $jsonRes['yoursPrinciplesSummarySorted'] = $yoursPrinciplesSummarySorted;
        $jsonRes['othersPrinciplesSummarySorted'] = $othersPrinciplesSummarySorted;

        return $jsonRes;
    }

}
