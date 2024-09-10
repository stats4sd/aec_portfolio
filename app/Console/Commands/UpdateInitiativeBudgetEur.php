<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\ExchangeRate;
use Illuminate\Console\Command;

class UpdateInitiativeBudgetEur extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-initiative-budget-eur';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update initiatives budget in EUR';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // remove global scope applied to Project model, soft delete is still applied
        $projects = Project::withoutGlobalScope('organisation')->where('exchange_rate_eur', null)->get();

        foreach ($projects as $project) {
            $this->info('Processing project with ID ' . $project->id);

            if ($project->currency == 'EUR') {
                $this->info('Initiative currency is EUR, copy budget to budget_eur');
                $project->exchange_rate_eur = 1.0;
                $project->budget_eur = $project->budget;
                $project->save();
            } else {

                $exchangeRate = $this->getEURExchangeRateFor($project->currency, $project->start_date);
                $project->exchange_rate_eur = $exchangeRate->rate;
                $project->budget_eur = $project->budget * $exchangeRate->rate;
                $project->save();

            }
        }

        $this->info(count($projects) . ' initatives processed');

        $this->info('done!');
    }

    private function getEURExchangeRateFor($projectCurrency, $date): ?ExchangeRate
    {
        // find exchange rate from project currency to EUR on project start date
        $exchangeRate = ExchangeRate::where('base_currency_id', $projectCurrency)->where('target_currency_id', 'EUR')->where('date', $date)->first();

        if(!$exchangeRate) {
            $this->info('Cannot find exchange rate for ' . $projectCurrency . ' => EUR, on ' . $date);

            if($date === '2020-01-01') {
                $this->error('Cannot find exchange rate for ' . $projectCurrency . ' => EUR, on ' . $date . ' and no fallback date available');
                return null;
            }

            // try for the day before
            $exchangeRate = $this->getEURExchangeRateFor($projectCurrency, $date->subDay());
        }

    }

}
