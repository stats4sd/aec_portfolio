<?php

namespace App\Exports\InitiativeImportTemplate;

use App\Models\Organisation;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class InitiativeImportTemplateExportWorkbook implements WithMultipleSheets
{

    public function __construct(public Organisation $organisation)
    {
    }

    public function sheets(): array
    {
        return [
            new InitiativeImportTemplateExportSheet($this->organisation),
            new InitiativeCategoryExportSheet(),
            new GeographicReachesExportSheet(),
            new CountriesExportSheet(),
            new YesNoExportSheet(),
        ];
    }

}
