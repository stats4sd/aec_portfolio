<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DataVisualisationDashboardController extends Controller
{

    public function show()
    {
        logger("DataVisualisationDashboardController.show()");

        // call stored procedure to get dashboard summary data
        DB::select("call generate_dashboard_summary(null, 3, 4, 5, 2020, 2023, 500000, 2000000, @status, @message, @statusSummary, @redlinesSummary, @principlesSummary)");
        $results = DB::select('select @status as status, @message as message, @statusSummary as statusSummary, @redlinesSummary as redlinesSummary, @principlesSummary as principlesSummary');

        logger($results);
        logger($results[0]->status);
        logger($results[0]->message);
        logger($results[0]->statusSummary);
        logger($results[0]->redlinesSummary);
        logger($results[0]->principlesSummary);

        // convert string to JSON
        $principlesSummaryJson = json_decode($results[0]->principlesSummary);
        logger($principlesSummaryJson);
        
        // access data items in JSON
        foreach ($principlesSummaryJson as $principlesSummaryItem) {
            logger($principlesSummaryItem->name);
        }


        // sort by score ASC
        $principlesSummarySortByScoreAsc = json_decode($results[0]->principlesSummary, true);
        usort($principlesSummarySortByScoreAsc, function($a, $b) {
            return $a['score'] > $b['score'];
        });
        logger($principlesSummarySortByScoreAsc);
        
        // sort by score DESC
        $principlesSummarySortByScoreDesc = json_decode($results[0]->principlesSummary, true);
        usort($principlesSummarySortByScoreDesc, function($a, $b) {
            return $a['score'] <= $b['score'];
        });
        logger($principlesSummarySortByScoreDesc);


        return;

    }

}
