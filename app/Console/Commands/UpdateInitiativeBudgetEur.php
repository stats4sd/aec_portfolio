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
        $projects = Project::withoutGlobalScope('organisation')->where('budget_eur', 0)->get();

        foreach ($projects as $project) {
            $this->info('Processing project with ID ' . $project->id);

            if ($project->currency == 'EUR') {
                $this->info('Initiative currency is EUR, copy budget to budget_eur');
                $project->exchange_rate_eur = 1.0;
                $project->budget_eur = $project->budget;
                $project->save();
            } else {
                // find exchange rate from project currency to EUR on project start date
                $exchangeRate = ExchangeRate::where('base_currency_id', $project->currency)->where('target_currency_id', 'EUR')->where('date', $project->start_date->format('Y-m-d'))->first();

                if ($exchangeRate != null) {
                    $this->info('+ Found exchange rate for ' . $project->currency . ' => EUR, on ' . $project->start_date->format('Y-m-d'));
                    $project->exchange_rate_eur = $exchangeRate->rate;
                    $project->budget_eur = $project->budget * $exchangeRate->rate;
                    $project->save();
                } else {
                    $this->info('- Cannot find exchange rate for ' . $project->currency . ' => EUR, on ' . $project->start_date->format('Y-m-d'));
                }
            }
        }

        $this->info(count($projects) . ' initatives processed');

        $this->info('done!');
    }

}
