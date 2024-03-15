<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Services\DBLog;
use App\Models\Currency;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Jobs\WriteLogMessageToFile;
use App\Jobs\GetOneDayExchangeRates;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class WriteLogMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:write-log-message';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add jobs to the queue to write log message in Laravel log file.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        logger('WriteLogMessage.handle()...');

        Storage::disk('local')->append('example.txt', Carbon::now() . ' WriteLogMessage.handle()...');

        // get yesterday date
        $date = Carbon::now()->subDays(1)->toDateString();

        // remove existing exchange_rates records for the date, if any
        DB::table('exchange_rates')->where('date', $date)->delete();

        $currencies = Currency::all();

        foreach ($currencies as $currency) {
            WriteLogMessageToFile::dispatch($currency, $date);
            $this->comment('dispatched job for ' . $currency . ' and the date ' . $date);
            break;
        }

        $this->info('done!');
    }
}
