@extends(backpack_view('blank'))

@php
$defaultBreadcrumbs = [
trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
$crud->entity_name_plural => url($crud->route),
trans('backpack::crud.list') => false,
];

// if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
$breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
@endphp

@section('header')
<div class="container-fluid">
    <div class="px-4 py-2 mb-4 bg-light shadow-sm border border-primary d-flex flex-column justify-content-center align-items-center">
        <div class="font-weight-bold mb-2">IMPORT PROCESS</div>
        <div class="d-flex justify-content-center align-items-center">
            <div class="px-4 text-success">Step 1. Upload File</div>
            <div class="px-4 font-weight-bold">Step 2. Review list of initiatives</div>
            <div class="px-4">Step 3. Finalise</div>
        </div>

    </div>

    <h2>
        <span class="text-capitalize">Step 2: {!! $crud->getHeading() ?? $crud->entity_name_plural !!}</span>
        <br /><span class="font-lg">{!! $crud->getSubheading() ?? '' !!}</span>
    </h2>
</div>
@endsection

@section('content')
{{-- Default box --}}
<div class="row">

    {{-- THE ACTUAL CONTENT --}}
    <div class="{{ $crud->getListContentClass() }}">

        <div class="row mb-0 flex justify-content-between mx-2">
            <div class="">
                @if ( $crud->buttons()->where('stack', 'top')->count() || $crud->exportButtons())
                <div class="d-print-none {{ $crud->hasAccess('create')?'with-border':'' }}">

                    @include('crud::inc.button_stack', ['stack' => 'top'])

                </div>
                @endif
            </div>
            <div>
                <div id="datatable_search_stack" class="mt-sm-0 mt-2 d-print-none"></div>
            </div>
        </div>

        {{-- Backpack List Filters --}}
        @if ($crud->filtersEnabled())
        @include('crud::inc.filters_navbar')
        @endif

        <table
            id="crudTable"
            class="bg-white table table-striped table-hover nowrap rounded shadow-xs border-xs mt-2"
            data-responsive-table="{{ (int) $crud->getOperationSetting('responsiveTable') }}"
            data-has-details-row="{{ (int) $crud->getOperationSetting('detailsRow') }}"
            data-has-bulk-actions="{{ (int) $crud->getOperationSetting('bulkActions') }}"
            cellspacing="0">
            <thead>
                <tr>
                    {{-- Table columns --}}
                    @foreach ($crud->columns() as $column)
                    <th
                        data-orderable="{{ var_export($column['orderable'], true) }}"
                        data-priority="{{ $column['priority'] }}"
                        data-column-name="{{ $column['name'] }}"
                        {{--
                                data-visible-in-table => if developer forced field in table with 'visibleInTable => true'
                                data-visible => regular visibility of the field
                                data-can-be-visible-in-table => prevents the column to be loaded into the table (export-only)
                                data-visible-in-modal => if column apears on responsive modal
                                data-visible-in-export => if this field is exportable
                                data-force-export => force export even if field are hidden
                                --}}

                        {{-- If it is an export field only, we are done. --}}
                        @if(isset($column['exportOnlyField']) && $column['exportOnlyField']===true)
                        data-visible="false"
                        data-visible-in-table="false"
                        data-can-be-visible-in-table="false"
                        data-visible-in-modal="false"
                        data-visible-in-export="true"
                        data-force-export="true"
                        @else
                        data-visible-in-table="{{var_export($column['visibleInTable'] ?? false)}}"
                        data-visible="{{var_export($column['visibleInTable'] ?? true)}}"
                        data-can-be-visible-in-table="true"
                        data-visible-in-modal="{{var_export($column['visibleInModal'] ?? true)}}"
                        @if(isset($column['visibleInExport']))
                        @if($column['visibleInExport']===false)
                        data-visible-in-export="false"
                        data-force-export="false"
                        @else
                        data-visible-in-export="true"
                        data-force-export="true"
                        @endif
                        @else
                        data-visible-in-export="true"
                        data-force-export="false"
                        @endif
                        @endif>
                        {{-- Bulk checkbox --}}
                        @if($loop->first && $crud->getOperationSetting('bulkActions'))
                        {!! View::make('crud::columns.inc.bulk_actions_checkbox')->render() !!}
                        @endif
                        {!! $column['label'] !!}
                    </th>
                    @endforeach

                    @if ( $crud->buttons()->where('stack', 'line')->count() )
                    <th data-orderable="false"
                        data-priority="{{ $crud->getActionsColumnPriority() }}"
                        data-visible-in-export="false"
                        data-action-column="true">{{ trans('backpack::crud.actions') }}</th>
                    @endif
                </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
                <tr>
                    {{-- Table columns --}}
                    @foreach ($crud->columns() as $column)
                    <th>
                        {{-- Bulk checkbox --}}
                        @if($loop->first && $crud->getOperationSetting('bulkActions'))
                        {!! View::make('crud::columns.inc.bulk_actions_checkbox')->render() !!}
                        @endif
                        {!! $column['label'] !!}
                    </th>
                    @endforeach

                    @if ( $crud->buttons()->where('stack', 'line')->count() )
                    <th>{{ trans('backpack::crud.actions') }}</th>
                    @endif
                </tr>
            </tfoot>
        </table>

        @if ( $crud->buttons()->where('stack', 'bottom')->count() )
        <div id="bottom_buttons" class="d-print-none text-center text-sm-left">
            @include('crud::inc.button_stack', ['stack' => 'bottom'])

            <div id="datatable_button_stack" class="float-right text-right hidden-xs"></div>
        </div>
        @endif

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

@section('after_styles')
{{-- DATA TABLES --}}
<link rel="stylesheet" type="text/css" href="{{ asset('packages/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('packages/datatables.net-fixedheader-bs4/css/fixedHeader.bootstrap4.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('packages/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}">

{{-- CRUD LIST CONTENT - crud_list_styles stack --}}
@stack('crud_list_styles')
@endsection

@section('after_scripts')
@include('crud::inc.datatables_logic')

{{-- CRUD LIST CONTENT - crud_list_scripts stack --}}
@stack('crud_list_scripts')
@endsection