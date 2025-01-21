@if ($crud->hasAccess('import'))
<a href="{{ url($crud->route.'/import') }}" class="btn btn-success" data-style="zoom-in"><span class="ladda-label"></i> Import {{ $crud->entity_name }}</span></a>
@endif