@if ($crud->hasAccess('import'))
<a href="{{ url($crud->route.'/discard-import') }}" class="btn btn-danger"><i class="fa fa-trash"></i> Discard import</a>
@endif
