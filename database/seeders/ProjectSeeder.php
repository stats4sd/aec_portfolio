<?php

namespace Database\Seeders;

use App\Enums\AssessmentStatus;
use App\Models\Assessment;
use App\Models\Organisation;
use App\Models\Portfolio;
use App\Models\Principle;
use App\Models\Project;
use App\Models\RedLine;
use App\Models\Region;
use Carbon\Carbon;
use Illuminate\Container\Container;
use Faker\Generator;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{

    protected $faker;

    public function __construct()
    {
        $this->faker = $this->withFaker();
    }

    // enable use of faker directly in seeder
    protected function withFaker()
    {
        return Container::getInstance()->make(Generator::class);
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organisations = Organisation::factory(['has_additional_criteria' => 0])->count(2)->create();

        foreach ($organisations as $organisation) {

            $portfolios = Portfolio::factory(['organisation_id' => $organisation->id])
                ->count(random_int(1, 4))
                ->create();

            foreach ($portfolios as $portfolio) {
                $projects = Project::factory([
                    'portfolio_id' => $portfolio->id,
                    'organisation_id' => $organisation->id
                ])
                    ->count(random_int(20, 50))
                    ->create();
            }
        }


        $redlines = RedLine::all();
        $principles = Principle::all();
        $regions = Region::with('countries')->get();


        // add complete assessments for projects
        foreach (Project::withoutGlobalScopes()->get() as $project) {

            // assign 80% of projects to regions
            if ($this->faker->boolean(80)) {
                $project->regions()->sync($this->faker->randomElements(
                    $regions->pluck('id')->toArray(),
                    $this->faker->numberBetween(1, 3)
                ));

                if ($this->faker->boolean(80)) {

                    $countriesInRegions = $project->regions->map(fn($region) => $region->countries)->flatten()->pluck('id');

                    $project->countries()->sync($this->faker->randomElements(
                        $countriesInRegions->toArray(),
                        $this->faker->numberBetween(1, $countriesInRegions->count())
                    ));
                }
            }

            // add redlines
            $redlinesToAdd = $redlines
                ->pluck('id')
                ->mapWithKeys(fn($id) => [$id => ['value' => $this->faker->boolean(1) ? 1 : 0]]);

            $assessment = $project->assessments->first();

            if (!$assessment) {
                continue;
            }

            // take 20% of projects to be not started;
            if ($this->faker->boolean(20)) {
                $assessment->redline_status = AssessmentStatus::NotStarted;
                $assessment->principle_status = AssessmentStatus::NotStarted;
                $assessment->additional_status = AssessmentStatus::Na;
                $assessment->save();
                continue;
            }

            $assessment->redLines()->sync($redlinesToAdd);

            // if redlines are failed
            if ($assessment->failingRedlines()->count() !== 0) {

                $assessment->redline_status = AssessmentStatus::Failed;
                $assessment->principle_status = AssessmentStatus::Na;
                $assessment->additional_status = AssessmentStatus::Na;
                $assessment->completed_at = Carbon::now();
                $assessment->save();
                continue;

            }

            // take 20% of projects to be redline only
            if ($this->faker->boolean(20)) {
                $assessment->redline_status = AssessmentStatus::Complete;
                $assessment->principle_status = AssessmentStatus::NotStarted;
                $assessment->additional_status = AssessmentStatus::Na;
                $assessment->completed_at = Carbon::now();
                $assessment->save();
                continue;

            }

            // add principle assessment
            $principlesToAdd = $principles
                ->mapWithKeys(function ($principle) {

                    $isNa = false;

                    if ($principle->can_be_na) {
                        $isNa = $this->faker->boolean(10);
                    }


                    return [$principle->id => [
                        'rating' => !$isNa ? $this->faker->numberBetween(0, 20) / 10 : null,
                        'rating_comment' => !$isNa ? $this->faker->sentences(4, true) : null,
                        'is_na' => $isNa,
                    ]];
                });

            $assessment->principles()->sync($principlesToAdd);
            $assessment->principle_status = AssessmentStatus::Complete;


            $assessment->additional_status = AssessmentStatus::Na;
            $assessment->redline_status = AssessmentStatus::Complete;
            $assessment->completed_at = Carbon::now();
            $assessment->save();

        }

    }

}
