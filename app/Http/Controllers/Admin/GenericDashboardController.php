<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class GenericDashboardController extends Controller
{

    public function show(Request $request)
    {
        logger("GenericDashboardController.show()...");

        // get organisation object from session
        $organisation = Session::get('selectedOrganisation');

        // TODO: error handling if no organisation selected yet

        return view('generic-dashboard.new-dashboard', [
            'organisation' => $organisation,
        ]);
    }

    public function find()
    {
        logger("GenericDashboardController.find()");

        // call stored procedure to get dashboard summary data
        DB::select("call generate_dashboard_summary(998, 999, 9, 20, null, null, null, null, null, null, @status, @message, @statusSummary, @redlinesSummary, @yoursPrinciplesSummary, @othersPrinciplesSummary)");

        $results = DB::select('select @status as status, @message as message, @statusSummary as statusSummary, @redlinesSummary as redlinesSummary, @yoursPrinciplesSummary as yoursPrinciplesSummary, @othersPrinciplesSummary as othersPrinciplesSummary');

        $status = $results[0]->status;
        $message = $results[0]->message;

        // convert string to JSON
        $statusSummary = json_decode($results[0]->statusSummary, true);
        $redlinesSummary = json_decode($results[0]->redlinesSummary, true);
        $yoursPrinciplesSummary = json_decode($results[0]->yoursPrinciplesSummary, true);
        $othersPrinciplesSummary = json_decode($results[0]->othersPrinciplesSummary, true);

        dump($results);

        dump($status);
        dump($message);

        // access JSON, array record, array element
        dump($statusSummary);
        dump($statusSummary[0]);
        dump($statusSummary[0]['status']);

        dump($redlinesSummary);
        dump($yoursPrinciplesSummary);
        dump($othersPrinciplesSummary);

        dump("==========");

        $yoursPrinciplesSummarySorted = $yoursPrinciplesSummary;      
        $othersPrinciplesSummarySorted = [];

        $sortBy = 1;    // highest to lowest score (highest green)
        // $sortBy = 2;    // lowest to highest score (highest red)
        // $sortBy = 3;    // default (order by principle number)

       
        // highest to lowest score (highest green)
        if ($sortBy == 1) {
            usort($yoursPrinciplesSummarySorted, function($a, $b) {
                return $a['green'] <= $b['green'];
            });

            // copy others principle summary item one by one according to the ordering of yours principles summary
            foreach ($yoursPrinciplesSummarySorted as $yoursItem) {
                foreach ($othersPrinciplesSummary as $othersItem) {
                    if ($othersItem['id'] == $yoursItem['id']) {
                        array_push($othersPrinciplesSummarySorted, $othersItem);
                        break;
                    }
                }
            }

        // lowest to highest score (highest red)
        } else if ($sortBy == 2) {
            usort($yoursPrinciplesSummarySorted, function($a, $b) {
                return $a['red'] <= $b['red'];
            });

            // copy others principle summary item one by one according to the ordering of yours principles summary
            foreach ($yoursPrinciplesSummarySorted as $yoursItem) {
                foreach ($othersPrinciplesSummary as $othersItem) {
                    if ($othersItem['id'] == $yoursItem['id']) {
                        array_push($othersPrinciplesSummarySorted, $othersItem);
                        break;
                    }
                }
            }

        // default (order by principle number)
        } else if ($sortBy == 3) {
            $othersPrinciplesSummarySorted = $othersPrinciplesSummary;
        }


        $yoursId = '';
        $yoursGreen = '';
        $yoursRed = '';
        $othersId = '';
        $othersGreen = '';
        $othersRed = '';

        foreach ($yoursPrinciplesSummarySorted as $yoursItem) {
            $yoursId = $yoursId . $yoursItem['id'] . ',';
            $yoursGreen = $yoursGreen . $yoursItem['green'] . ',';
            $yoursRed = $yoursRed . $yoursItem['red'] . ',';
        }

        foreach ($othersPrinciplesSummarySorted as $othersItem) {
            $othersId = $othersId . $othersItem['id'] . ',';
            $othersGreen = $othersGreen . $othersItem['green'] . ',';
            $othersRed = $othersRed . $othersItem['red'] . ',';
        }

        dump($yoursId);
        dump($othersId);
        dump($yoursGreen);
        dump($othersGreen);
        dump($yoursRed);
        dump($othersRed);

        dump($yoursPrinciplesSummarySorted);
        dump($othersPrinciplesSummarySorted);

        return;
    }

}
