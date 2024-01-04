<?php

namespace App\Exports\InitiativeImportTemplate;

use App\Enums\GeographicalReach;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;

class GeographicReachesExportSheet implements FromCollection, WithTitle
{
    public function collection(): Collection
    {
        return collect(GeographicalReach::cases())
            ->map(fn(GeographicalReach $enum) => [$enum->name]);


    }

    public function title(): string
    {
        return 'geographic_reaches';
    }

}
