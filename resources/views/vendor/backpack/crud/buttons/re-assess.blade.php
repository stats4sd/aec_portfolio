@if ($crud->hasAccess('update'))
	<a
    @if($entry->assessments->last()->assessment_status === \App\Enums\AssessmentStatus::Complete)
            href="{{ url($crud->route.'/'.$entry->id.'/re-assess') }}"
            class="btn btn-info"
        @else
            class="btn btn-info disabled"
        @endif
        data-button-type="assess"
    >
        <i class="la la-question"></i> Re-Assess Project
    </a>
@endif
