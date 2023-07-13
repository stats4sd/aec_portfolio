<?php

namespace App\Exports\Assessment;

use App\Models\AdditionalCriteriaAssessment;
use App\Models\Organisation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Matrix\Operators\Addition;

class AdditionalCriteriaExport implements FromCollection, WithHeadings, WithTitle
{

    public function __construct(public Organisation $organisation)
    {
    }

    public function collection()
    {
        return AdditionalCriteriaAssessment::with([
            'assessment.project.portfolio',
            'additionalCriteria',
            'additionalCriteriaScoreTags',
            'additionalCriteriaCustomScoreTags',
        ])
            ->withoutGlobalScopes(['organisation'])
            ->whereHas('assessment', function (Builder $query) {
                $query->whereHas('project', function (Builder $query) {
                    $query->where('organisation_id', $this->organisation->id);
                });
            })
            ->get()
            ->map(fn(AdditionalCriteriaAssessment $additionalCriteriaAssessment): Collection => collect([
                'organisation_id' => $this->organisation->id,
                'organisation_name' => $this->organisation->name,
                'portfolio_id' => $additionalCriteriaAssessment->assessment->project->portfolio_id,
                'portfolio_name' => $additionalCriteriaAssessment->assessment->project->portfolio->name,
                'initiative_id' => $additionalCriteriaAssessment->assessment->project->id,
                'initiative_name' => $additionalCriteriaAssessment->assessment->project->name,
                'principle' => $additionalCriteriaAssessment->additionalCriteria->name,
                'is_na' => $additionalCriteriaAssessment->is_na,
                'rating' => $additionalCriteriaAssessment->rating,
                'comments' => $additionalCriteriaAssessment->rating_comment,
                'indicators_present' => $additionalCriteriaAssessment->additionalCriteriaScoreTags->pluck('name')->join(', '),
                'additional_indicators' => $additionalCriteriaAssessment->additionalCriteriaCustomScoreTags->pluck('name')->join(', '),
            ]));
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
            'criteria',
            'is_na',
            'rating',
            'comments',
            'indicators_present',
            'additional_indicators',
        ];
    }

    public function title(): string
    {
        return 'additional_criteria_assessment';
    }
}
