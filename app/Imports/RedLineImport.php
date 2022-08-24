<?php

namespace App\Imports;

use App\Models\RedLine;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class RedLineImport implements ToModel, WithHeadingRow, WithUpserts
{
    public function model(array $row)
    {
        return new RedLine([
            'name' => $row['name'],
            'description' => $row['description'],
        ]);
    }

    public function uniqueBy()
    {
        return 'name';
    }
}
