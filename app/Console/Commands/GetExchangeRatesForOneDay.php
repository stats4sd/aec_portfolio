<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Currency;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Jobs\GetOneDayExchangeRates;
use Illuminate\Support\Facades\Http;

class GetExchangeRatesForOneDay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-one-day-exchange-rates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add jobs to the queue to get historical exchange rates for one day only.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // get yesterday date
        $date = Carbon::now()->subDays(1)->toDateString();

        // remove existing exchange_rates records for the date, if any
        DB::table('exchange_rates')->where('date', $date)->delete();

        $currencies = Currency::all();

        foreach ($currencies as $currency) {
            GetOneDayExchangeRates::dispatch($currency, $date);
            $this->comment('dispatched job for ' . $currency . ' and the date ' . $date);
        }

        $this->info('done!');

    }

}
