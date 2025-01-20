<?php

namespace App\Imports;

use App\Models\Portfolio;
use Maatwebsite\Excel\Row;
use App\Models\TempProject;
use Illuminate\Support\Str;
use App\Models\TempProjectImport;
use App\Models\InitiativeCategory;
use App\Http\Requests\ProjectRequest;
use App\Http\Requests\TempProjectRequest;
use Maatwebsite\Excel\Concerns\OnEachRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class TempProjectImporter implements OnEachRow, WithHeadingRow, SkipsEmptyRows, WithCalculatedFormulas, WithValidation
{

    use Importable;

    protected array $ignoreCodes;

    public function __construct(public Portfolio $portfolio, public TempProjectImport $tempProjectImport)
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

        // TODO: validate each column in this row, return all validation results as a string
        // Question: can we re-use ProjectRequest class and get all validation result as string?

        // TODO: validation
        $validationResult = null;
        // $validationResult = $this->validate($data);

        $valid = ($validationResult == '');

        $tempProject = TempProject::create([
            'temp_project_import_id'  => $this->tempProjectImport->id,
            'portfolio_id' => $this->portfolio->id,
            'organisation_id' => $this->portfolio->organisation_id,
            'code' => $data['code'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'currency' => $data['currency'],
            'exchange_rate' => $data['exchange_rate'],
            'exchange_rate_eur' => $data['exchange_rate_eur'],
            'budget' => $data['budget'],
            'budget_eur' => $data['budget'] * $data['exchange_rate_eur'],
            'uses_only_own_funds' => $usesOnlyOwnFunds,
            'main_recipient' => $data['main_recipient'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'geographic_reach' => $data['geographic_reach'],

            'valid' => $valid,

            // Question: how to store and show multiple lines validation result?
            'validation_result' => $validationResult,
        ]);
    }


    private function validate($data)
    {
        $validationResult = '';

        $validationResult = $validationResult . $this->checkRequired('Name', $data['name']);

        // The existing project import does not handle initiative category
        $validationResult = $validationResult . $this->checkRequired('Category', $data['category']);

        $validationResult = $validationResult . $this->checkRequired('Currency', $data['currency']);
        $validationResult = $validationResult . $this->checkRequired('Exchange rate', $data['exchange_rate']);
        $validationResult = $validationResult . $this->checkRequired('Exchange rate eur', $data['exchange_rate_eur']);
        $validationResult = $validationResult . $this->checkRequired('Budget', $data['budget']);
        $validationResult = $validationResult . $this->checkRequired('Uses only own funds', $data['uses_only_own_funds']);
        $validationResult = $validationResult . $this->checkRequired('Main recipient', $data['main_recipient']);
        $validationResult = $validationResult . $this->checkRequired('Start date', $data['start_date']);
        $validationResult = $validationResult . $this->checkRequired('Geographic reach', $data['geographic_reach']);
        $validationResult = $validationResult . $this->checkRequired('Continent 1', $data['continent_1']);

        return $validationResult;
    }


    // existing check
    private function checkRequired($fieldName, $fieldValue)
    {
        if ($fieldValue == null || $fieldValue == '') {
            return $fieldName . ' is required.<br/>';
        } else {
            return '';
        }
    }

    // TODO: data type check

    // TODO: reasonable check




    public function rules(): array
    {
        return (new TempProjectRequest())->rules();
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

        $data['initiativeCategory'] = InitiativeCategory::where('name', $data['category'])->first()?->id;

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
