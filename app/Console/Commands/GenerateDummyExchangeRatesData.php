<?php

namespace App\Console\Commands;

use App\Models\Tool;
use App\Models\Theme;
use App\Models\Metric;
use App\Models\Currency;
use App\Models\Dimension;
use Illuminate\Support\Str;
use App\Models\ExchangeRate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportHistoricExchangeRateData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-dummy-exchange-rate-data {year} {no_of_days}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate dummy exchange rate data (with same currency in base currency and target currency) with specified date range';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('start');

        $year = $this->argument('year');
        $noOfDays = $this->argument('no_of_days');

        $this->comment('year: ' . $year);
        $this->comment('noOfDays: ' . $noOfDays);

        $currencies = Currency::all();

        foreach ($currencies as $currency) {
            $this->comment($currency->id);
            for ($i = 0; $i < $noOfDays; $i++) {
                $date = date_create($year . '-01-01');
                date_add($date, date_interval_create_from_date_string($i . ' days'));
                // $this->comment(date_format($date, "Y-m-d"));

                $exchangeRate = ExchangeRate::create([
                    'base_currency_id' => $currency->id,
                    'target_currency_id' => $currency->id,
                    'rate' => 1,
                    'date' => $date,
                ]);

                // break;
            }

            // break;
        }


        $this->info('done!');
    }
}
