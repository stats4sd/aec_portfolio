@extends(backpack_view('blank'))

@php
$defaultBreadcrumbs = [
trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
$crud->entity_name_plural => url($crud->route),
trans('backpack::crud.add') => false,
];
// if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
$breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
@endphp

@section('header')

<section class="container-fluid">
    <div class="px-4 py-2 mb-4 bg-light shadow-sm border border-primary d-flex flex-column justify-content-center align-items-center">
        <div class="font-weight-bold mb-2">IMPORT PROCESS</div>
        <div class="d-flex justify-content-center align-items-center">
            <div class="px-4 font-weight-bold">Step 1. Upload File</div>
            <div class="px-4">Step 2. Review list of initiatives</div>
            <div class="px-4">Step 3. Finalise</div>
        </div>

    </div>
    <h2>
        <span class="text-capitalize">Step 1: {!! $crud->getHeading() ?? 'Import '.$crud->entity_name_plural !!}</span>
    </h2>
</section>
@endsection

@section('content')

<div class="row">
    <div class="{{ $crud->getCreateContentClass() }}">
        <!-- Default box -->

        <ul class="list-group">
            @if($errors->count() > 0)
            <li class="list-group-item list-group-item-primary">There were errors when importing the Excel file. No records have been imported.
            </li>
            @endif
            @foreach ($errors->all() as $message)
            <li class="list-group-item list-group-item-accent-danger">{{ $message }}</li>
            @endforeach
        </ul>

        <form method="post"
            action="{{ url($crud->route.'/import') }}"
            enctype="multipart/form-data">
            {!! csrf_field() !!}
            <!-- load the view from the application if it exists, otherwise load the one in the package -->
            @if(view()->exists('vendor.backpack.crud.form_content'))
            @include('vendor.backpack.crud.form_content', [ 'fields' => $crud->fields(), 'action' => 'import' ])
            @else
            @include('crud::form_content', [ 'fields' => $crud->fields(), 'action' => 'import' ])
            @endif


            <a href="javascript:void(0)" data-toggle="modal" data-target="#discardImport" data-route="{{ url($crud->route.'/discard-import') }}" class="btn btn-danger" data-button-type="discard"><i class="la la-trash"></i> Discard Import</a>
            <button type="submit" class="btn btn-success">
                <span class="la la-save" role="presentation" aria-hidden="true"></span> &nbsp;
                <span data-value="saveImport">Submit and go to Step 2</span>
            </button>

        </form>
    </div>
</div>

<div class="modal fade" id="discardImport" tabindex="-1" role="dialog" aria-labelledby="discardImportLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="discardImportLabel">Discard Import</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you wish to discard this import? This will delete all pending initiative entries. You may restart the import at any time by re-uploading the same Excel file.</span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form action="{{ route('import.discard') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary">Confirm Discard</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection