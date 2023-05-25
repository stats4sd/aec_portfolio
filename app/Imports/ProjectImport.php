<?php

namespace App\Imports;

use App\Enums\GeographicalReach;
use App\Http\Requests\ProjectRequest;
use App\Models\Organisation;
use App\Models\Portfolio;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ProjectImport implements ToModel, WithHeadingRow, SkipsEmptyRows, WithCalculatedFormulas, WithValidation
{

    use Importable;

    public function __construct(public Portfolio $portfolio)
    {
    }

    public function model(array $row)
    {

        // skip instructions and example row from template;
        if (isset($row['code']) && ($row['code'] === 'enter a unique code for the initiative' || $row['code'] === 'example')) {
            return null;
        }

        $startDate = null;
        $endDate = null;

        if ($row['start_date']) {
            $startDate = Date::excelToDateTimeObject($row['start_date']);
        }

        if ($row['end_date']) {
            $endDate = Date::excelToDateTimeObject($row['end_date']);
        }


        return new Project([
            'portfolio_id' => $this->portfolio->id,
            'organisation_id' => $this->portfolio->organisation_id,
            'code' => $row['code'],
            'name' => $row['name'],
            'description' => $row['description'] ?? null,
            'currency' => $row['currency'],
            'budget' => $row['budget'],
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

    }

    public function rules(): array
    {
        return (new ProjectRequest())->rules();
    }

    // FIXME: This hack is only here until the isEmptyWhen() functionality is added to the toModel import (https://github.com/SpartnerNL/Laravel-Excel/pull/3828).
    // Until then, we need to manually make sure the instructions and example rows pass validation...
    public function prepareForValidation($data, $index)
    {
        Log::info($data);

        if (isset($data['code']) && ($data['code'] === 'enter a unique code for the initiative' || $data['code'] === 'example')) {
            return [
                'organisation_id' => 1,
                'portfolio_id' => 1,
                'code' => 'example',
                'name' => 'something for required',
                'budget'  => 1234,
                'currency' => 'TTT',
                'start_date' => '1900-01-01',
                'geographic_reach' => GeographicalReach::Global->name,
            ];
        }

        $data['portfolio_id'] = $this->portfolio->id;
        $data['organisation_id'] = $this->portfolio->organisation_id;

        return $data;
    }


}
