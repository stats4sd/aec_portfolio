@if ($crud->hasAccess('update'))
	<a href="{{ url($crud->route.'/'.$entry->id.'/redline') }}" class="btn btn-warning" data-button-type="red-lines"><i class="la la-question"></i> Review Redlines</a>
@endif
