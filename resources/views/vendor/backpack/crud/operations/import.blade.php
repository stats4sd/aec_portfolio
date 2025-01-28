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
                <div class="px-4 font-weight-bold">1. Upload File</div>
                <div class="px-4">2. Review list of initiatives</div>
                <div class="px-4">3. Finalise</div>
            </div>

        </div>
        <h2>
            <span class="text-capitalize">{!! $crud->getHeading() ?? 'Import '.$crud->entity_name_plural !!}</span>
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
                        <br>
                        Each error with the row number is listed below. Each one should be checked in the Excel file before attempting to re-upload.
                    </li>
                @endif
                @foreach ($errors->all() as $message)
                    <li class="list-group-item list-group-item-accent-danger">{{ $message }}</li>
                @endforeach
            </ul>

            <form method="post"
                  action="{{ url($crud->route.'/import') }}"
                  enctype="multipart/form-data"
            >
                {!! csrf_field() !!}
                <!-- load the view from the application if it exists, otherwise load the one in the package -->
                @if(view()->exists('vendor.backpack.crud.form_content'))
                    @include('vendor.backpack.crud.form_content', [ 'fields' => $crud->fields(), 'action' => 'import' ])
                @else
                    @include('crud::form_content', [ 'fields' => $crud->fields(), 'action' => 'import' ])
                @endif


                <a href="{{ url($crud->route.'/discard-import') }}" class="btn btn-danger"><i class="fa fa-trash"></i> Discard import</a>
                <button type="submit" class="btn btn-success">
                    <span class="la la-save" role="presentation" aria-hidden="true"></span> &nbsp;
                    <span data-value="saveImport">Submit File</span>
                </button>

            </form>
        </div>
    </div>

@endsection
