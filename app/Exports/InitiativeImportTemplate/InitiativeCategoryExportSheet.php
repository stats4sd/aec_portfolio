<?php

namespace App\Exports\InitiativeImportTemplate;

use App\Models\InitiativeCategory;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;

class InitiativeCategoryExportSheet implements FromQuery, WithTitle
{

    public function query(): Builder
    {
        return InitiativeCategory::query()->select('name');
    }

    public function title(): string
    {
        return 'initiative_categories';
    }
}
