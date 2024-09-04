<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\ProjectCrudController;
use App\Models\Organisation;
use App\Models\Portfolio;
use App\Models\Project;
use App\Models\User;
use Illuminate\Console\Command;

class ClonePortfolio extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clone-portfolio';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clones an entire portfolio of projects, assessments, and scores. Written to cover a request from GIZ. For now this will remain a server-side operation that can be conducted on request. In the future we may make this a feature in the UI.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // ask which organisation this is for - choose from a list of organisations (pull from database)
        $organisations = Organisation::all()->pluck('name', 'id');

        $this->info('Which organisation is this for?');
        $organisation = $this->choice('Choose an organisation', $organisations->toArray());

        $organisationId = $organisations->search($organisation);

        $this->info('You chose: ' . $organisationId);

        // ask which portfolio to clone - choose from a list of portfolios
        $portfolios = Portfolio::withoutGlobalScope('organisation')->where('organisation_id', $organisationId)->get()->pluck('name', 'id');

        if ($portfolios->count() === 0) {
            $this->error('No portfolios found for this organisation.');
            return;
        }

        $this->info('Which portfolio would you like to clone?');
        $portfolio = $this->choice('Choose a portfolio', $portfolios->toArray());

        $portfolioId = $portfolios->search($portfolio);

        $this->info('You chose: ' . $portfolioId);


        // get all projects for the portfolio
        $this->info('~~~~~~~~~~~');
        $this->info('Projects found in this portfolio');

        $projects = Project::withoutGlobalScope('organisation')->where('portfolio_id', $portfolioId)->get();

        $projects->each(function ($project) {
            $this->comment('Project: ' . $project->name);
        });

        $this->info('~~~~~~~~~~~');

        // check if the user is sure they want to clone this portfolio
        if (!$this->confirm('Are you sure you want to clone this portfolio?')) {
            $this->info('Cancelled.');
            return;
        }

        // create a new portfolio
        $newPortfolio = Portfolio::create([
            'name' => $portfolio . ' (clone)',
            'organisation_id' => $organisationId,
        ]);

        // log in as an admin user
        $admin = User::whereHas('roles', function ($query) {
            $query->where('name', 'Site Admin');
        })->first();

        auth()->login($admin);


        $this->info('Cloning portfolio...');

        // clone each project
        $projects->each(function(Project $project) use ($newPortfolio) {

            $this->comment('Cloning project: ' . $project->name);

            $projectCrud = new ProjectCrudController;

            $newProject = $projectCrud->duplicate($project);

            // update name and code of the project
            $newProject->name = $project->name . ' (clone)';
            $newProject->code = $project->code . '-clone';
            $newProject->portfolio_id = $newPortfolio->id;

            $newProject->save();

        });

        auth()->logout();

        $this->info('Done!');

    }
}
