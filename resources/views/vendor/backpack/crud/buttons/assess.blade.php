@if ($crud->hasAccess('update'))
    <!-- DONE - TODO: check overall score and assessment status of latest assessment instead of project -->
	<a
    @if($entry->assessments->last()->overall_score === 0 || $entry->assessments->last()->assessment_status === \App\Enums\AssessmentStatus::NotStarted || $entry->assessments->last()->assessment_status === \App\Enums\AssessmentStatus::RedlinesIncomplete)
            class="btn btn-info disabled"
        @else
            href="{{ url($crud->route.'/'.$entry->id.'/assess') }}"
            class="btn btn-info"
        @endif
        data-button-type="assess"
    >
        <i class="la la-question"></i> Assess Project
    </a>
@endif
