<?php

namespace App\Console\Commands;

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
        $response = Http::withHeaders([
            'apiKey' => config('services.currency.api-key')
        ])
            ->get('https://api.freecurrencyapi.com/v1/historical', [
                'base_currency' => 'EUR',
                'date_from' => '2000-01-01',
                'date_to' => '2002-01-01',
            ]);


        dd($response->json());
    }
}
