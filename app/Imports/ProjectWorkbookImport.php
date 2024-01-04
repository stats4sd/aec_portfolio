<?php

namespace App\Imports;

use App\Http\Requests\ProjectRequest;
use App\Models\Organisation;
use App\Models\Portfolio;
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
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithUpserts;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ProjectWorkbookImport implements WithMultipleSheets
{

    public function __construct(public Portfolio $portfolio)
    {
    }

    public function sheets(): array
    {
        return [
            'initiatives' => new ProjectImport($this->portfolio),
        ];
    }

}
