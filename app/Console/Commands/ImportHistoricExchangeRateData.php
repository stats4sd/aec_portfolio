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
    protected $signature = 'app:import-historic-exchange-rate-data {currency}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import CSV file for historic exchange rate data of a specified currency';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('start');

        // loop all combinations of all currencies, excluding HRK and the currency itself
        // $outerCurrencies = Currency::where('id', '!=', 'HRK')->orderBy('id')->get();

        // get the specified currency from command line argument
        $currency = $this->argument('currency');
        $outerCurrencies = Currency::where('id', '=', $currency)->get();

        foreach ($outerCurrencies as $outerCurrency) {
            $innerCurrencies = Currency::where('id', '!=', 'HRK')->where('id', '!=', $outerCurrency->id)->orderBy('id')->get();

            $this->info('===== ' . $outerCurrency->id . ' =====');

            foreach ($innerCurrencies as $innerCurrency) {
                // construct filename of csv file
                $filename = 'storage/csv/exchange_rate/' . $outerCurrency->id . '/' . $outerCurrency->id . $innerCurrency->id . '.csv';

                // read csv file into collection
                $data = $this->readCsvFileIntoCollection($filename);

                $this->comment('Importing ' . $outerCurrency->id . ' => ' . $innerCurrency->id . ', ' . count($data) . ' records...');

                foreach ($data as $row) {
                    $day = substr($row['Date'], 0, 2);
                    $month = substr($row['Date'], 3, 2);
                    $year = substr($row['Date'], 6, 4);
                    $date = $year . '-' . $month . '-' . $day;

                    $exchangeRate = ExchangeRate::create([
                        'base_currency_id' => $outerCurrency->id,
                        'target_currency_id' => $innerCurrency->id,
                        'rate' => $row['Close'],
                        'date' => $date,
                    ]);

                    // break;
                }

                // break;
            }

            // break;
        }


        $this->info('done!');
    }


    public function readCsvFileIntoCollection($filename)
    {
        // TODO: the original csv file cannot be read properly, possible cause should be related to tidiness of some rows

        // Read CSV file content, call trim() to remove last blank line
        $csvFileContent = trim(File::get($filename));

        // remove newlines within cells
        $csvFileContent = preg_replace('/\"(.+)\n\"/', '$1', $csvFileContent);


        // Split by new line. Use the PHP_EOL constant for cross-platform compatibility.
        $lines = explode(PHP_EOL, $csvFileContent);

        // Extract the header and convert it into a Laravel collection.
        $header = collect(str_getcsv(array_shift($lines)));

        // Convert the rows into a Laravel collection.
        $rows = collect($lines);

        // Map through the rows and combine them with the header to produce the final collection.
        $data = $rows->map(function ($row) use ($header) {
            return $header->combine(str_getcsv($row));
        });

        return $data;
    }
}
