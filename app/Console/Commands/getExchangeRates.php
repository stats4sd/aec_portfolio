<?php

namespace App\Console\Commands;

use App\Jobs\GetHistoricExchangeRates;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class getExchangeRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-exchange-rates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get exchange rates for a currency for 2000 - 2010';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $years = range(2000, 2005);

        $currencies = ['EUR', 'USD'];

        foreach($currencies as $currency) {
            foreach($years as $year) {
                GetHistoricExchangeRates::dispatch($currency, $year);

                $this->comment('dispatched job for ' . $currency . ' and ' . $year);
            }
        }

        $this->info('done!');

    }

}
