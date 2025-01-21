<?php

namespace App\Imports;

use App\Models\Portfolio;
use App\Models\TempProject;
use App\Models\TempProjectImport;
use Maatwebsite\Excel\Row;
use Illuminate\Support\Str;
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

        // do custom validation
        $validationResult = $this->validate($data);

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

        return $validationResult;
    }

    // check for required field
    private function checkRequired($fieldName, $fieldValue)
    {
        if ($fieldValue == null || $fieldValue == '') {
            return $fieldName . ' is required.<br/>';
        } else {
            return '';
        }
    }

    // check for positive integer
    private function checkPositiveInteger($fieldName, $fieldValue)
    {
        if ($fieldValue != null && !ctype_digit($fieldValue)) {
            return $fieldName . ' needs to be a positive integer.<br/>';
        } else {
            return '';
        }
    }

    // check length of currency
    private function checkLength($fieldName, $fieldValue, $length)
    {
        if (Str::length($fieldValue) != $length) {
            return $fieldName . ' needs to be ' . $length . ' characters long.<br/>';
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

        if ($endDate < $startDate) {
            return $date2Name . ' needs to be later than ' . $date1Name . '.<br/>';
        } else {
            return '';
        }
    }

    // Note:
    // TempProjectRequest does not have any validation rules, so that non empty project records will be imported as temp_projects records for user review
    public function rules(): array
    {
        return (new TempProjectRequest())->rules();
    }

    // define how to determine if a row is considered as an empty row
    public function isEmptyWhen(array $row): bool
    {
        return in_array($row['code'], $this->ignoreCodes, true) ||
            ($row['code'] === null && $row['name'] === null);
    }
}
