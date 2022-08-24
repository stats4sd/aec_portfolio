@if ($crud->hasAccess('update'))
	<a href="{{ url($crud->route.'/'.$entry->id.'/assess') }}" class="btn btn-info" data-button-type="red-lines"><i class="la la-question"></i> Assess Project</a>
@endif
