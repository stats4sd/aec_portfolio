<?php

namespace App\Exports\Assessment;

use App\Models\Organisation;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

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
            new AdditionalCriteriaExport($this->organisation),
            new RegionProjectExport($this->organisation),
            new CountryProjectExport($this->organisation),
        ];
    }
}
