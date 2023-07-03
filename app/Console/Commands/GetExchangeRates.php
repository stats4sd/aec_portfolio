<?php

namespace App\Console\Commands;

use App\Jobs\GetHistoricExchangeRates;
use App\Models\Currency;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class GetExchangeRates extends Command
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
    protected $description = 'Add jobs to the queue to get *all* historical exchanges rates';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        if(!$this->confirm('This will add hundreds of jobs to the queue, to retrieve the exchanges rates between 32 currencies for every day from Jan 01 2000 to the current day. Are you sure you want to do this?')) {
            $this->info('Aborting!');
            return;
        }


        $today = Carbon::now();
        $thisYear = $today->year;

        $years = range(2000, $thisYear);

        $currencies = Currency::all();

        foreach($currencies as $currency) {
            foreach($years as $year) {
                GetHistoricExchangeRates::dispatch($currency, $year);
                $this->comment('dispatched job for the currency ' . $currency . ' and the year ' . $year);
            }
        }

        $this->info('done!');

    }

}
