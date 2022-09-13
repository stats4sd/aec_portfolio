<?php

namespace App\Imports;

use App\Models\Principle;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class PrincipleImport implements ToModel, WithHeadingRow, WithUpserts, SkipsEmptyRows
{
    public function model(array $row)
    {

        return new Principle([
            'name' => $row['name'],
            'number' => $row['number'],
            'rating_two' => $row['rating_two_definition'],
            'rating_one' => $row['rating_one_definition'],
            'rating_zero' => $row['rating_zero_definition'],
            'rating_na' => $row['rating_na_definition'],
            'can_be_na' => $row['can_be_na'],
        ]);
    }

    public function uniqueBy()
    {
        return 'number';
    }
}
