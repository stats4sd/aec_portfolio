<?php

namespace App\Console\Commands;

use App\Models\Project;
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
            $project->budget_eur = $project->budget * $project->exchange_rate;
            $project->save();
        }

        $this->info(count($projects) . ' initatives processed');

        $this->info('done!');
    }

}
