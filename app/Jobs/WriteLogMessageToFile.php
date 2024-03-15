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
use Illuminate\Support\Facades\Storage;

class WriteLogMessageToFile implements ShouldQueue
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

    public function retryUntil(): \DateTime
    {
        return now()->addMinutes(1);
    }

    /**
     * Execute the job.
     * @throws RequestException
     */
    public function handle(): void
    {
        logger('WriteLogMessageToFile.handle()...');

        Storage::disk('local')->append('example.txt', Carbon::now() . ' WriteLogMessageToFile.handle()...');
    }
}
