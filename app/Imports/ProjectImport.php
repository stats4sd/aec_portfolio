<?php

namespace App\Imports;

use App\Models\Organisation;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ProjectImport implements ToCollection, WithHeadingRow
{

    public function __construct(public Organisation $organisation)
    {
    }

    public function collection(Collection $collection)
    {

        foreach ($collection as $row) {

            if ($row['code'] === 'enter a unique code for the project' || $row['code'] === 'example') {
                continue;
            }

            $startDate = null;
            $endDate = null;

            if ($row['start_date']) {
                $startDate = Date::excelToDateTimeObject($row['start_date']);
            }

            if ($row['end_date']) {
                $endDate = Date::excelToDateTimeObject($row['end_date']);
            }


            $project = Project::create([
                'code' => $row['code'],
                'name' => $row['name'],
                'description' => $row['description'] ?? null,
                'currency' => $row['currency'],
                'budget' => $row['budget'],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'organisation_id' => $this->organisation->id ?? null,
            ]);
        }


    }


}
