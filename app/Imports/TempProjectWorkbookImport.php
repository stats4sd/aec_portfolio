<?php

namespace App\Imports;

use App\Models\Portfolio;
use App\Models\TempProjectImport;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TempProjectWorkbookImport implements WithMultipleSheets
{

    public function __construct(public Portfolio $portfolio, public TempProjectImport $tempProjectImport) {}

    public function sheets(): array
    {
        return [
            'initiatives' => new TempProjectImporter($this->portfolio, $this->tempProjectImport),
        ];
    }
}
