<?php

namespace App\Services;

use App\Models\Project;
use Carbon\Carbon;
use FreeCurrencyApi\FreeCurrencyApi\FreeCurrencyApiClient;
use Illuminate\Support\Facades\Http;

class FreeCurrencyApiHelper
{



    public static function convert(Project $project): int
    {
        $budget = $project->budget;

        if($project->exchange_rate) {
            $project->budget_org = $project->budget * $project->exchange_rate;

        }

        else {

            // if the project starts in the future, use today's date.
            $date = (new Carbon($project->start_date))->isBefore(Carbon::now()) ? $project->start_date : Carbon::now()->toDateString();

            $response = Http::withHeaders([
                'apiKey' => config('services.currency.api-key')
            ])
            ->get('https://api.freecurrencyapi.com/v1/historical', [
                'base_currency' => $project->currency,
                'currencies' => $project->organisation->currency,
                'date_from' => $date,
                'date_to' => $date,
            ]);

            dd($response);
        }



        return $budget;
    }

    public static function getApiClient(): FreeCurrencyApiClient
    {
        return new FreeCurrencyApiClient(config('services.currency.api-key'));
    }

}
