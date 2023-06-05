<?php

namespace Database\Factories;

use App\Enums\AssessmentStatus;
use App\Enums\GeographicalReach;
use App\Models\Organisation;
use App\Models\Portfolio;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'code' => $this->faker->word(),
            'description' => $this->faker->text(),
            'budget' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'currency' => $this->faker->currencyCode(),
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now(),
            'geographic_reach' => $this->faker->randomElement(GeographicalReach::cases()),
            'sub_regions' => $this->faker->words(5, true),

            'organisation_id' => Organisation::factory(),
            'portfolio_id' => Portfolio::factory(),
        ];
    }
}
