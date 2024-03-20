<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Currency;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Jobs\GetOneDayExchangeRates;
use Illuminate\Support\Facades\Http;

class GetMissingExchangeRatesForOneDay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-missing-one-day-exchange-rates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add jobs to the queue to get historical exchange rates for one day that is missing in database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // find the most popular project start date without exchange rate eur
        // Assumptions: 
        // 1. Free Currency data is available since 2000-01-01
        // 2. Exclude yesterday project start date, as it is possible that yesterday exchange rate is now being retrieved
        $sql1 = "SELECT start_date, COUNT(*) as no_of_records".
               " FROM projects " . 
               " WHERE exchange_rate_eur IS NULL" .
               " AND start_date BETWEEN '2000-01-01' AND DATE_SUB(CURDATE(), INTERVAL 1 DAY)" .
               " AND UPPER(currency) IN (SELECT id FROM currencies)" .
               " GROUP BY start_date" .
               " ORDER BY COUNT(*) DESC, start_date;";

        $projectStartDates = DB::select($sql1);

        $this->info('There are ' . count($projectStartDates) . ' project start dates with missitng exchange_rate_eur.');
        $this->info('Handle the most popular project start date: ' . $projectStartDates[0]->start_date);


        // check number of exchange rate records for a specifc date
        // P.S. only handle one day to avoid exceeding FreeCurrency monthly request limit
        $sql2 = "SELECT COUNT(*) as no_of_records FROM exchange_rates WHERE date = '" . $projectStartDates[0]->start_date . "';";

        $exchangeRatesCount = DB::select($sql2);

        $this->info('There are ' . $exchangeRatesCount[0]->no_of_records . ' exchange rate records for ' . $projectStartDates[0]->start_date);


        // a complete set of exchange rate data = 33 * 33 = 1089 records
        if ($exchangeRatesCount[0]->no_of_records == 1089) {
            $this->info('We already have the complete set of exchange rate data for ' . $projectStartDates[0]->start_date . '. This is not necessary to retrieve exchange rate data again.');

        } else {
            $this->info('Retrieve exchange rate data for ' . $projectStartDates[0]->start_date);

            // get the most popular project start date without exchange_rate_eur
            $date = $projectStartDates[0]->start_date;

            // remove existing exchange_rates records for the date, if any
            DB::table('exchange_rates')->where('date', $date)->delete();

            $currencies = Currency::all();

            foreach ($currencies as $currency) {
                GetOneDayExchangeRates::dispatch($currency, $date);
                $this->comment('dispatched job for ' . $currency . ' and the date ' . $date);
            }

        }

        $this->info('done!');

    }

}
