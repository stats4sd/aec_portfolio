@if ($crud->hasAccess('update'))
	<a href="{{ backpack_url('assessment/'.$entry->assessments->last()->id .'/redline') }}" class="btn btn-warning" data-button-type="red-lines"><i class="la la-question"></i> Review Redlines</a>
@endif
