@if ($crud->hasAccess('import'))
<a href="{{ url($crud->route.'/discard-import') }}" class="btn btn-danger" data-style="zoom-in"><span class="ladda-label"></i> Discard import</span></a>
@endif