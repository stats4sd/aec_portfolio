<?php

namespace Database\Seeders;

use App\Enums\AssessmentStatus;
use App\Models\Assessment;
use App\Models\Organisation;
use App\Models\Portfolio;
use App\Models\Principle;
use App\Models\Project;
use App\Models\RedLine;
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
        $organisations = Organisation::factory()->count(3)->create();

        foreach ($organisations as $organisation) {

            $portfolios = Portfolio::factory(['organisation_id' => $organisation->id])
                ->count(random_int(1, 4))
                ->create();

            foreach ($portfolios as $portfolio) {
                $projects = Project::factory([
                    'portfolio_id' => $portfolio->id,
                    'organisation_id' => $organisation->id
                ])
                    ->count(random_int(0, 50))
                    ->create();
            }
        }

        $redlines = Redline::all();
        $principles = Principle::all();


        // add complete assessments for projects
        foreach (Project::all() as $project) {

            // add redlines
            $redlinesToAdd = $redlines
                ->pluck('id')
                ->mapWithKeys(fn($id) => [$id => ['value' => $this->faker->boolean(1) ? 1 : 0]]);

            $assessment = $project->assessments->first();

            if (!$assessment) {
                continue;
            }

            $assessment->redlines()->sync($redlinesToAdd);

            if ($assessment->failingRedlines->count() === 0) {

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

            }

            $assessment->assessment_status = AssessmentStatus::Complete;
            $project->assessment_status = AssessmentStatus::Complete;

        }

    }

}
