@if ($crud->hasAccess('import'))
<a href="{{ url($crud->route.'/import') }}" class="btn btn-primary" data-style="zoom-in"><span class="ladda-label"></i>Back to Step 1: Re-upload file</span></a>
@endif
