<?php

namespace App\Imports;

use App\Http\Requests\ProjectRequest;
use App\Models\Organisation;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ProjectImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{

    use Importable;

    public function __construct(public Organisation $organisation)
    {
    }

    public function model(array $row)
    {

            $startDate = null;
            $endDate = null;

            if ($row['start_date']) {
                dump($row);
                $startDate = Date::excelToDateTimeObject($row['start_date']);
            }

            if ($row['end_date']) {
                $endDate = Date::excelToDateTimeObject($row['end_date']);
            }


            return new Project([
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


    // skip instructions and example entry;
    public function isEmptyWhen(array $row): bool
    {
        return $row['code'] === 'enter a unique code for the initiative' || $row['code'] === 'example';
    }

    public function rules(): array
    {
        return (new ProjectRequest())->rules();
    }







}
