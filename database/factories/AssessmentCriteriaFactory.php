<?php

namespace Database\Factories;

use App\Models\AdditionalCriteria;
use App\Models\Organisation;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssessmentCriteriaFactory extends Factory
{
    protected $model = AdditionalCriteria::class;

    public function definition(): array
    {
        return [
            'organisation_id' => Organisation::factory(),
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentences(3, true),
            'link' => $this->faker->url,
            'rating_two' => $this->faker->sentences(3, true),
            'rating_one' => $this->faker->sentences(3, true),
            'rating_zero' => $this->faker->sentences(3, true),
            'rating_na' => $this->faker->sentences(3, true),
            'can_be_na' => $this->faker->boolean,
            'parent_id' => null,
        ];
    }
}
