<?php

namespace App\Console\Commands;

use App\Jobs\GetHistoricExchangeRates;
use App\Models\Currency;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class GetExchangeRatesForTesting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-test-exchange-rates {year}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add jobs to the queue to get historical exchange rates for one year only.';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $year = $this->argument('year');

        $currencies = Currency::all();

        foreach ($currencies as $currency) {
            GetHistoricExchangeRates::dispatch($currency, $year);
            $this->comment('dispatched job for ' . $currency . ' and the year ' . $year);
        }

        $this->info('done!');

    }

}
