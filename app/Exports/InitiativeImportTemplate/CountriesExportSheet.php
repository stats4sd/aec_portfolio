<?php

namespace App\Exports\InitiativeImportTemplate;

use App\Models\Country;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use PHPUnit\Framework\Constraint\Count;

class CountriesExportSheet implements FromCollection, WithTitle
{


    public function collection(): Collection
    {
        return Country::with(['region', 'continent'])->get()->map(fn(Country
        $country) => [
            'continent' => $country->continent?->name,
            'region' => $country->region?->name,
            'name' => $country->name,
        ])->sortBy('name')->values()->unique('name');
    }

    public function title(): string
    {
        return 'countries';
    }
}
