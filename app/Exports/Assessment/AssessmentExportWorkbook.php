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
        $sheets = [
            new InitiativesExport($this->organisation),
            new RedlinesExport($this->organisation),
            new PrincipleExport($this->organisation),
        ];

        if ($this->organisation->has_additional_criteria) {
            $sheets[] = new AdditionalCriteriaExport($this->organisation);
        }

        $sheets[] = new RegionProjectExport($this->organisation);
        $sheets[] = new CountryProjectExport($this->organisation);

        return $sheets;
    }
}
