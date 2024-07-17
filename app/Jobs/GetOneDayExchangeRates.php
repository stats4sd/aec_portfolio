<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Services\DBLog;
use App\Models\Currency;
use App\Models\ExchangeRate;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Queue\Middleware\ThrottlesExceptions;

class GetOneDayExchangeRates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Currency $currency, public string $date)
    {
        //
        DBLog::info('GetOneDayExchangeRates', 'SYSTEM', 'construct() start');
    }

    // what middleware should the job pass through?
    public function middleware(): array
    {
        DBLog::info('GetOneDayExchangeRates', 'SYSTEM', 'middleware() start');

        return [
            new RateLimited('exchange_rates'),
            //new ThrottlesExceptions(1, 1),
        ];
    }

    public function retryUntil(): \DateTime
    {
        DBLog::info('GetOneDayExchangeRates', 'SYSTEM', 'retryUntil() start, add 10 minutes');

        return now()->addMinutes(10);
    }

    /**
     * Execute the job.
     * @throws RequestException
     */
    public function handle(): void
    {
        DBLog::info('GetOneDayExchangeRates', 'SYSTEM', 'handle() start');

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
            ->throw(function (Response $response, RequestException $exception) {
                Log::error($exception->getMessage());
                DBLog::error('GetOneDayExchangeRates', 'SYSTEM', $exception->getMessage());
            })
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
        DBLog::debug('GetOneDayExchangeRates', 'SYSTEM', 'create ' . count($rates) . ' exchange rate records');

        DBLog::info('GetOneDayExchangeRates', 'SYSTEM', 'handle() end');
    }
}
