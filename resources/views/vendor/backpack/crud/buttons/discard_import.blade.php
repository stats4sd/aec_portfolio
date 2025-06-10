@if ($crud->hasAccess('list'))
	<a href="javascript:void(0)" data-toggle="modal" data-target="#discardImport" data-route="{{ url($crud->route.'/discard-import') }}" class="btn btn-danger" data-button-type="discard"><i class="la la-trash"></i>  Discard Import</a>
@endif
