<?php

namespace Database\Factories;

use App\Enums\AssessmentStatus;
use App\Enums\GeographicalReach;
use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Models\InitiativeCategory;
use App\Models\Organisation;
use App\Models\Portfolio;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Spatie\Browsershot\Exceptions\HtmlIsNotAllowedToContainFile;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $initiativeCategory = $this->faker->randomElement(InitiativeCategory::all()->pluck('id')->toArray());
        if($initiativeCategory === 5) {
            $initiativeCategoryOther = $this->faker->text();
        } else {
            $initiativeCategoryOther = null;
        }

        $currency = $this->faker->randomElement(Currency::all()->pluck('id')->toArray());


        return [
            'name' => $this->faker->bs(),
            'code' => $this->faker->word(),
            'description' => $this->faker->text(),
            'initiative_category_id' => $initiativeCategory,
            'initiative_category_other' => $initiativeCategoryOther,
            'budget' => $this->faker->randomNumber(),
            'created_at' => $this->faker->dateTimeBetween('- 2 years', 'now'),
            'updated_at' => Carbon::now(),
            'currency' => $this->faker->randomElement(Currency::all()->pluck('id')->toArray()),
            'exchange_rate_eur' => 1,
            'exchange_rate' => 1,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now(),
            'geographic_reach' => $this->faker->randomElement(GeographicalReach::cases()),
            'sub_regions' => $this->faker->words(5, true),

            'organisation_id' => Organisation::factory(),
            'portfolio_id' => Portfolio::factory(),
        ];
    }
}
