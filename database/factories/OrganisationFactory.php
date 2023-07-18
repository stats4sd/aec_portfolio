<?php

namespace Database\Factories;

use App\Enums\GeographicalReach;
use App\Models\Country;
use App\Models\Currency;
use App\Models\InstitutionType;
use App\Models\Organisation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class OrganisationFactory extends Factory
{
    protected $model = Organisation::class;

    public function definition(): array
    {
        $institutionType = $this->faker->randomElement(InstitutionType::all()->pluck('id')->toArray());
        if ($institutionType === 5) {
            $institutionTypeOther = $this->faker->text();
        } else {
            $institutionTypeOther = null;
        }

        return [
            'name' => $this->faker->company,
            'currency' => $this->faker->randomElement(Currency::all()->pluck('id')->toArray()),
            'institution_type_id' => $institutionType,
            'institution_type_other' => $institutionTypeOther,
            'hq_country' => $this->faker->randomElement(Country::all()->pluck('id')->toArray()),
            'geographic_reach' => $this->faker->randomElement(GeographicalReach::cases()),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
