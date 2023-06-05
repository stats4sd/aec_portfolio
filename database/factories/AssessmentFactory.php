<?php

namespace Database\Factories;

use App\Enums\AssessmentStatus;
use App\Models\Assessment;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AssessmentFactory extends Factory
{
    protected $model = Assessment::class;

    public function definition(): array
    {
        return [
            'assessment_status' =>  $this->faker->randomElement(AssessmentStatus::cases()),
            'completed_at' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'project_id' => Project::factory(),
        ];
    }
}
