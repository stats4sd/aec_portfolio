<?php

namespace App\Imports;

use App\Models\Portfolio;
use Maatwebsite\Excel\Row;
use App\Models\TempProject;
use Illuminate\Support\Str;
use App\Models\TempProjectImport;
use Maatwebsite\Excel\Concerns\OnEachRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class TempProjectImporter implements OnEachRow, WithHeadingRow, SkipsEmptyRows, WithCalculatedFormulas
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

        // collect up locations
        // Note: considering TempProject model is stored to show the validation result for making corrections.
        // I would recommend to store continents, regions, countries in CSV format for simplicity and readability.

        $continentKeys = collect($data)
            ->keys()
            ->map(fn($key) => Str::startsWith($key, 'continent_') ? $key : null)
            ->filter(fn($key) => $key !== null);

        $data['continentsArray'] = $continentKeys->map(fn($key) => $data[$key])->filter(fn($continent) => $continent !== null)->toArray();

        $data['continents'] = implode(', ', array_values($data['continentsArray']));

        $regionKeys = collect($data)
            ->keys()
            ->map(fn($key) => Str::startsWith($key, 'region_') ? $key : null)
            ->filter(fn($key) => $key !== null);

        $data['regionsArray'] = $regionKeys->map(fn($key) => $data[$key])->filter(fn($region) => $region !== null)->toArray();

        $data['regions'] = implode(', ', array_values($data['regionsArray']));

        $countryKeys = collect($data)
            ->keys()
            ->map(fn($key) => Str::startsWith($key, 'country_') ? $key : null)
            ->filter(fn($key) => $key !== null);

        $data['countriesArray'] = $countryKeys->map(fn($key) => $data[$key])->filter(fn($country) => $country !== null)->toArray();

        $data['countries'] = implode(', ', array_values($data['countriesArray']));


        // do custom validation
        $validationResult = $this->validate($data);

        $valid = ($validationResult == '');

        $tempProject = TempProject::create([
            'temp_project_import_id'  => $this->tempProjectImport->id,
            'portfolio_id' => $this->portfolio->id,
            'organisation_id' => $this->portfolio->organisation_id,
            'code' => $data['code'],
            'name' => $data['name'],
            'category' => $data['category'],
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
            'continents' => $data['continents'],
            'regions' => $data['regions'],
            'countries' => $data['countries'],
            'valid' => $valid,
            'validation_result' => $validationResult,
        ]);
    }


    private function validate($data)
    {
        // Note:
        // user either leave empty or select one option for below fields with selection box in excel file:
        // category, geographic_reach, continent_1, continent_2, region_1, region_2, country_1, country_2, country_3, country_4
        // assume it is not necessary to have custom validation for them

        $validationResult = '';

        $validationResult = $validationResult . $this->checkRequired('Name', $data['name']);

        $validationResult = $validationResult . $this->checkRequired('Category', $data['category']);

        $validationResult = $validationResult . $this->checkRequired('Currency', $data['currency']);
        $validationResult = $validationResult . $this->checkLength('Currency', $data['currency'], 3);

        $validationResult = $validationResult . $this->checkRequired('Exchange rate', $data['exchange_rate']);

        $validationResult = $validationResult . $this->checkRequired('Exchange rate eur', $data['exchange_rate_eur']);

        $validationResult = $validationResult . $this->checkRequired('Budget', $data['budget']);
        $validationResult = $validationResult . $this->checkPositiveInteger('Budget', $data['budget']);

        $validationResult = $validationResult . $this->checkRequired('Uses only own funds', $data['uses_only_own_funds']);

        $validationResult = $validationResult . $this->checkRequired('Main recipient', $data['main_recipient']);

        $validationResult = $validationResult . $this->checkRequired('Start date', $data['start_date']);

        $validationResult = $validationResult . $this->checkDateAfterAnotherDate('Start date', 'End date', $data['start_date'], $data['end_date']);

        $validationResult = $validationResult . $this->checkRequired('Geographic reach', $data['geographic_reach']);

        $validationResult = $validationResult . $this->checkRequired('Continent 1', $data['continent_1']);

        $validationResult.= $this->checkUnique('Project Code', $data['code']);

        return $validationResult;
    }

    private function checkUnique($fieldName, $fieldValue) {

        $projectCodes = $this->portfolio->organisation->projects->pluck('code')->unique();

        if($fieldValue && $projectCodes->contains($fieldValue)) {
            return "<li>The {$fieldName}: {$fieldValue} already exists in your organisation.</li>";
        }
    }

    // check for required field
    private function checkRequired($fieldName, $fieldValue)
    {
        if ($fieldValue == null || $fieldValue == '') {
            return '<li>' . $fieldName . ' is required.</li>';
        } else {
            return '';
        }
    }

    // check for positive integer
    private function checkPositiveInteger($fieldName, $fieldValue)
    {
        if ($fieldValue != null && !ctype_digit($fieldValue)) {
            return '<li>' . $fieldName . ' needs to be a positive integer.</li>';
        } else {
            return '';
        }
    }

    // check length of currency
    private function checkLength($fieldName, $fieldValue, $length)
    {
        if (Str::length($fieldValue) != $length) {
            return '<li>' . $fieldName . ' needs to be ' . $length . ' characters long.</li>';
        } else {
            return '';
        }
    }

    // check if date2 is later than date1
    private function checkDateAfterAnotherDate($date1Name, $date2Name, $date1, $date2)
    {
        $startDate = null;
        $endDate = null;

        if ($date1) {
            $startDate = Date::excelToDateTimeObject($date1);
        }

        if ($date2) {
            $endDate = Date::excelToDateTimeObject($date2);
        }

        if ($endDate && $endDate < $startDate) {
            return '<li>' . $date2Name . ' needs to be later than ' . $date1Name . '.</li>';
        } else {
            return '';
        }
    }

    // define how to determine if a row is considered as an empty row
    public function isEmptyWhen(array $row): bool
    {
        return in_array($row['code'], $this->ignoreCodes, true) ||
            ($row['code'] === null && $row['name'] === null);
    }
}
