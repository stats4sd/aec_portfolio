@if ($crud->hasAccess('update'))
	<a
        @if($entry->overall_score === 0 || $entry->assessment_status === \App\Enums\AssessmentStatus::NotStarted || $entry->assessment_status === \App\Enums\AssessmentStatus::RedlinesIncomplete)
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
