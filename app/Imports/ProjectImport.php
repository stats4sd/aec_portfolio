<?php

namespace App\Imports;

use App\Models\Organisation;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class ProjectImport implements ToModel, WithHeadingRow, WithCalculatedFormulas
{

    public function __construct(public Organisation $organisation)
    {
    }

    public function model(array $row)
    {

        return new Project([
            'code' => $row['code'],
            'name' => $row['name'],
            'description' => $row['description'],
            'budget' => $row['budget_usd'],
            'organisation_id' => $this->organisation->id ?? null,
        ]);
    }


}
