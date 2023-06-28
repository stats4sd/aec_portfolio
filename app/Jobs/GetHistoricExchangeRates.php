<?php

namespace App\Jobs;

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

class GetHistoricExchangeRates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $currency, public int $year)
    {
        //
    }

    // what middleware should the job pass through?
    public function middleware(): array
    {
        return [
            new RateLimited('exchange_rates'),
            new ThrottlesExceptions(2, 1),
            ];
    }

    /**
     * Execute the job.
     * @throws RequestException
     */
    public function handle(): void
    {
        $startDate = $this->year . '-01-01';
        $endDate = ($this->year + 1) . '-01-01';

        $response = Http::withHeaders([
            'apiKey' => config('services.currency.api-key')
        ])
            ->get('https://api.freecurrencyapi.com/v1/historical', [
                'base_currency' => $this->currency,
                'date_from' => $startDate,
                'date_to' => $endDate,
            ])
        ->throw()
        ->json();



    }
}
