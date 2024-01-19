<?php

namespace App\Imports;

use App\Enums\GeographicalReach;
use App\Http\Requests\ProjectRequest;
use App\Models\Continent;
use App\Models\Country;
use App\Models\InitiativeCategory;
use App\Models\Organisation;
use App\Models\Portfolio;
use App\Models\Project;
use App\Models\Region;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Row;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ProjectImport implements OnEachRow, WithHeadingRow, SkipsEmptyRows, WithCalculatedFormulas, WithValidation
{

    use Importable;

    protected array $ignoreCodes;

    public function __construct(public Portfolio $portfolio)
    {
        $this->ignoreCodes = [
            'enter a unique code for the initiative.',
            'example',
            'optional',
            'required',
        ];
    }

    public function onRow(Row $row)
    {

        $data = $row->toArray();

        // skip instructions and example row from template;
        if (isset($data['code']) && in_array($data['code'], $this->ignoreCodes, true)) {
            return null;
        }

        $startDate = null;
        $endDate = null;

        if ($data['start_date']) {
            $startDate = Date::excelToDateTimeObject($data['start_date']);
        }

        if ($data['end_date']) {
            $endDate = Date::excelToDateTimeObject($data['end_date']);
        }

        if ($data['uses_only_own_funds'] == 'Yes') {
            $usesOnlyOwnFunds = 1;
        } else {
            $usesOnlyOwnFunds = 0;
        }

        $project = Project::create([
            'portfolio_id' => $this->portfolio->id,
            'organisation_id' => $this->portfolio->organisation_id,
            'code' => $data['code'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'currency' => $data['currency'],
            'exchange_rate' => $data['exchange_rate'],
            'budget' => $data['budget'],
            'budget_eur' => $data['budget'] * $data['exchange_rate'],
            'uses_only_own_funds' => $usesOnlyOwnFunds,
            'main_recipient' => $data['main_recipient'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'geographic_reach' => $data['geographic_reach'],
        ]);

        $continentIds = collect($data['continents'])
            ->map(fn($continent) => Continent::where('name', $continent)->first()?->id)
            ->toArray();

        $regionIds = collect($data['regions'])
            ->map(fn($region) => Region::where('name', $region)->first()?->id)
            ->toArray();

        $countryIds = collect($data['countries'])
            ->map(fn($country) => Country::where('name', $country)->first()?->id)
            ->toArray();

        $project->continents()->sync($continentIds);
        $project->regions()->sync($regionIds);
        $project->countries()->sync($countryIds);


    }

    public function rules(): array
    {
        return (new ProjectRequest())->rules();
    }

    public function isEmptyWhen(array $row): bool
    {
        return in_array($row['code'], $this->ignoreCodes, true) ||
            ($row['code'] === null && $row['name'] === null);
    }

    // FIXME: This hack is only here until the isEmptyWhen() functionality is added to the toModel import (https://github.com/SpartnerNL/Laravel-Excel/pull/3828).
    // Until then, we need to manually make sure the instructions and example rows pass validation...
    public function prepareForValidation($data, $index)
    {

        $data['portfolio_id'] = $this->portfolio->id;
        $data['organisation_id'] = $this->portfolio->organisation_id;

        $data['initiativeCategory'] =
            InitiativeCategory::where('name', $data['category'])->first()?->id;

        if (Str::lower($data['uses_only_own_funds']) === 'yes') {
            $data['uses_only_own_funds'] = 1;
        } else {
            $data['uses_only_own_funds'] = 0;
        }

        if ($data['start_date'] && (is_int($data['start_date']) || is_float($data['start_date']))) {
            $data['start_date'] = Date::excelToDateTimeObject($data['start_date']);
        }

        if ($data['end_date'] && (is_int($data['end_date']) || is_float($data['end_date']))) {
            $data['end_date'] = Date::excelToDateTimeObject($data['end_date']);
        }


        // collect up locations
        $continentKeys = collect($data)
            ->keys()
            ->map(fn($key) => Str::startsWith($key, 'continent_') ? $key : null)
            ->filter(fn($key) => $key !== null);

        $data['continents'] = $continentKeys->map(fn($key) => $data[$key])->filter(fn($continent) => $continent !== null)->toArray();

        $regionKeys = collect($data)
            ->keys()
            ->map(fn($key) => Str::startsWith($key, 'region_') ? $key : null)
            ->filter(fn($key) => $key !== null);

        $data['regions'] = $regionKeys->map(fn($key) => $data[$key])->filter(fn($region) => $region !== null)->toArray();

        $countryKeys = collect($data)
            ->keys()
            ->map(fn($key) => Str::startsWith($key, 'country_') ? $key : null)
            ->filter(fn($key) => $key !== null);

        $data['countries'] = $countryKeys->map(fn($key) => $data[$key])->filter(fn($country) => $country !== null)->toArray();

        return $data;
    }


}
