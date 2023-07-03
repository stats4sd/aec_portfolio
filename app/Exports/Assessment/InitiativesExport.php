<?php

namespace App\Exports\Assessment;

use App\Models\Organisation;
use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class InitiativesExport implements FromCollection, WithHeadings, WithTitle
{

    public function __construct(public Organisation $organisation)
    {
    }

    public function collection()
    {
        $org = $this->organisation->load(
            ['projects' => [
                'assessments' => ['additionalCriteria'],
                'regions',
                'countries',
                'continents',
                'portfolio',
            ],
            ]);

        return $org->projects->map(fn(Project $project) => [
            'organisation_id' => $this->organisation->id,
            'organisation_name' => $this->organisation->name,

            'portfolio_id' => $project->portfolio->id,
            'portfolio_name' => $project->portfolio->name,
            'initiative_id' => $project->id,
            'code' => $project->code,
            'name' => $project->name,
            'description' => $project->description,
            'currency' => $project->currency,
            'budget' => $project->budget,
            'currency_org' => $project->organisation->currency,
            'budget_org' => $project->budget_org,
            'start_date' => $project->start_date,
            'end_date' => $project->end_date,
            'geographic_reach' => $project->geographic_reach,
            'continents' => $project->continents->pluck('name')->join(', '),
            'regions' => $project->regions->pluck('name')->join(', '),
            'countries' => $project->countries->pluck('name')->join(', '),
            'sub_regions' => $project->sub_regions,
            'assessment_status' => $project->latest_assessment_status,
            'assessment_completed_at' => $project->latest_assessment->completed_at,
            'assessment_score' => $project->latest_assessment->overall_score,

        ]);
    }

    public function headings(): array
    {
        return [
            'organisation_id',
            'organisation_name',
            'portfolio_id',
            'portfolio_name',
            'initiative_id',
            'code',
            'name',
            'description',
            'currency',
            'budget',
            'currency_of_institution',
            'budget_in_institution_currency',
            'start_date',
            'end_date',
            'geographic_reach',
            'continents',
            'regions',
            'countries',
            'sub_regions',
            'assessment_status',
            'assessment_completed_at',
            'assessment_score',
        ];
    }

    public function title(): string
    {
        return 'initiatives';
    }
}
