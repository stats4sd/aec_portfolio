<?php

namespace App\Jobs;

use App\Models\Currency;
use App\Models\ExchangeRate;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\Middleware\ThrottlesExceptions;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GetOneDayExchangeRates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Currency $currency, public string $date)
    {
        //
    }

    // what middleware should the job pass through?
    public function middleware(): array
    {
        return [
            new RateLimited('exchange_rates'),
            //new ThrottlesExceptions(1, 1),
        ];
    }

    /**
     * Execute the job.
     * @throws RequestException
     */
    public function handle(): void
    {
        $startDate = $this->date;
        $endDate = $this->date;

        // api call
        $response = Http::withHeaders([
            'apiKey' => config('services.currency.api-key')
        ])
            ->get('https://api.freecurrencyapi.com/v1/historical', [
                'base_currency' => $this->currency->id,
                'date_from' => $startDate,
                'date_to' => $endDate,
            ])
            ->throw()
            ->json();


        $rates = [];

        foreach ($response['data'] as $date => $values) {

            foreach ($values as $key => $value) {

                $rates[] = [
                    'base_currency_id' => $this->currency->id,
                    'target_currency_id' => $key,
                    'date' => $date,
                    'rate' => $value,
                ];

            }
        }

        $this->currency->exchangeRates()->createMany($rates);

    }

}
