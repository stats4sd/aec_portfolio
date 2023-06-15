<?php

namespace App\Exports\Assessment;

use App\Models\Organisation;
use App\Models\PrincipleAssessment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class PrincipleExport implements FromCollection, WithHeadings, WithTitle
{
    public function __construct(public Organisation $organisation)
    {
    }


    public function collection()
    {
        return PrincipleAssessment::with([
            'assessment.project.portfolio',
            'principle',
            'scoreTags',
            'customScoreTags',
        ])
            ->whereHas('assessment', function(Builder $query) {
                $query->whereHas('project', function(Builder $query) {
                    $query->where('organisation_id', $this->organisation->id);
                });
            })
            ->get()
            ->map(fn(PrincipleAssessment $principleAssessment): Collection => collect([
                'portfolio_id' => $principleAssessment->assessment->project->portfolio_id,
                'portfolio_name' => $principleAssessment->assessment->project->portfolio->name,
                'initiative_id' => $principleAssessment->assessment->project->id,
                'initiative_name' => $principleAssessment->assessment->project->name,
                'principle'  => $principleAssessment->principle->name,
                'is_na' => $principleAssessment->is_na,
                'rating' => $principleAssessment->rating,
                'comments' => $principleAssessment->rating_comment,
                'indicators_present' => $principleAssessment->scoreTags->pluck('name')->join(', '),
                'additional_indicators' => $principleAssessment->customScoreTags->pluck('name')->join(', '),
            ]));
    }

    public function headings(): array
    {
        return [
                'portfolio_id',
                'portfolio_name',
                'initiative_id',
                'initiative_name',
                'principle' ,
                'is_na',
                'rating',
                'comments',
                'indicators_present',
                'additional_indicators',
            ];
    }

    public function title(): string
    {
        return 'principle_assessment';
    }
}
