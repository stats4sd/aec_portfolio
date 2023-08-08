<?php

namespace Database\Factories;

use App\Enums\GeographicalReach;
use App\Models\Organisation;
use App\Models\Portfolio;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PortfolioFactory extends Factory
{
    protected $model = Portfolio::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->catchPhrase(),
            'organisation_id' => Organisation::factory(),
            'budget' => $this->faker->randomNumber(),
            'description' => $this->faker->paragraph,
            'currency' => $this->faker->currencyCode(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
