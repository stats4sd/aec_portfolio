@if ($crud->hasAccess('update'))
	<a href="{{ url($crud->route.'/import-template') }}" class="btn btn-link" data-button-type="import-template"><i class="la la-download"></i> Downlaod Template for Imports</a>
@endif
