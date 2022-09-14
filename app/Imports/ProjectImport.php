<?php

namespace App\Imports;

use App\Models\Project;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class ProjectImport implements ToModel, WithHeadingRow
{

    public function model(array $row)
    {
        return new Project([
            'code' => $row['code'],
            'name' => $row['name'],
            'description' => $row['description'],
            'budget' => $row['budget_usd'],
        ]);
    }


}
