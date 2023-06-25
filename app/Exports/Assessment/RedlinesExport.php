<?php

namespace App\Exports\Assessment;

use App\Models\Organisation;
use App\Models\Project;
use App\Models\RedLine;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;

class RedlinesExport implements FromCollection, WithHeadings, WithTitle, WithStrictNullComparison
{
    public function __construct(public Organisation $organisation)
    {
    }

    public function collection()
    {
        $org = $this->organisation->load(
            'projects.assessments.redLines'
        );

        return $org->projects->map(function (Project $project) {

            return $project->assessments->last()->redlines->map(function ($redline) use ($project) {
                return collect([
                    'organisation_id' => $this->organisation->id,
                    'organisation_name' => $this->organisation->name,
                    'portfolio_id' => $project->portfolio_id,
                    'portfolio_name' => $project->portfolio->name,
                    'initiative_id' => $project->id,
                    'initiative_name' => $project->name,
                    'red_flag' => $redline->name,
                    'present' => $redline->pivot->value,
                    'redline_assessment_status' => $project->latest_assessment->redline_status,
                ]);
            });
        });

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
            'red_flag',
            'present',
            'redline_assessment_status',
        ];
    }

    public function title(): string
    {
        return 'redflag_assessment';
    }
}
