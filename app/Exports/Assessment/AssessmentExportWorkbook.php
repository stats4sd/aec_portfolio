<?php

namespace App\Exports\Assessment;

use App\Exports\CountryProjectExport;
use App\Exports\RegionProjectExport;
use App\Models\Organisation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PHPUnit\Framework\Constraint\Count;

class AssessmentExportWorkbook implements WithMultipleSheets
{

    public function __construct(public Organisation $organisation)
    {
    }

    public function sheets(): array
    {
        return [
            new InitiativesExport($this->organisation),
            new RedlinesExport($this->organisation),
            new PrincipleExport($this->organisation),
            new RegionProjectExport($this->organisation),
            new CountryProjectExport($this->organisation),
        ];
    }
}
