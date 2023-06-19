<?php

namespace App\Exports\Assessment;

use App\Models\Country;
use App\Models\Organisation;
use App\Models\Project;
use App\Models\Region;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class CountryProjectExport implements FromCollection, WithHeadings, WithTitle
{
    public function __construct(public Organisation $organisation)
    {
    }

    public function collection()
    {
        return $this->organisation->projects
            ->map(fn(Project $project) => $project->countries
                ->map(fn(Country $country) => [
                    'organisation_id' => $this->organisation->id,
                    'organisation_name' => $this->organisation->name,
                    'portfolio_id' => $project->portfolio_id,
                    'portfolio_name' => $project->portfolio->name,
                    'initiative_id' => $project->id,
                    'initiative_name' => $project->name,
                    'country_id' => $country->id,
                    'country_name' => $country->name,
                ])
            );
    }

    public function headings(): array
    {
        return [
            'organisation_id',
            'organisation_name',
            'portfolio_id',
            'portfolio_name',
            'initiative_id',
            'initiative_name',
            'country_id',
            'country_name',
        ];
    }

    public function title(): string
    {
        return 'country_project';
    }
}
