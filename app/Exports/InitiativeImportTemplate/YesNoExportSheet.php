<?php

namespace App\Exports\InitiativeImportTemplate;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;

class YesNoExportSheet implements FromCollection, WithTitle
{

    public function collection(): Collection
    {
        return collect([
            ['yes'],
            ['no'],
        ]);
    }

    public function title(): string
    {
        return 'yes_no';
    }
}
