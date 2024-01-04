<?php

namespace App\Exports\InitiativeImportTemplate;

use App\Models\Organisation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;

class PortfolioExportSheet implements FromQuery, WithTitle
{

    public function __construct(public Organisation $organisation)
    {
    }

    public function query(): HasMany
    {
        return $this->organisation->portfolios()->select('name');
    }

    public function title(): string
    {
        return 'portfolios';
    }
}
