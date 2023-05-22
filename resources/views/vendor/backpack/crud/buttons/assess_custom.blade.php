@if ($crud->hasAccess('update') && $entry->organisation->assessmentCriteria()->count() > 0)
	<a
    @if($entry->assessments->last()->overall_score === 0 || $entry->assessments->last()->assessment_status === \App\Enums\AssessmentStatus::NotStarted || $entry->assessments->last()->assessment_status === \App\Enums\AssessmentStatus::RedlinesIncomplete)
            class="btn btn-info disabled"
        @else
            href="{{ backpack_url('assessment/'.$entry->assessments->last()->id.'/assess-custom') }}"
            class="btn btn-info"
        @endif
        data-button-type="assess"
    >
        <i class="la la-question"></i> Assess Additional Criteria
    </a>
@endif
