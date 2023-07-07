@extends(backpack_view('blank'))

@section('content')

    <div class="container-fluid mt-16" id="initiativesListPage">
        <initiatives-list
            :organisation="{{ $organisation }}"
            :initiatives="{{ $projects->toJson() }}"
            :has-additional-assessment="{{ $has_additional_assessment ? 'true' : 'false' }}"
            :show-add-button="{{ $show_add_button ? 'true' : 'false' }}"
            :show-import-button="{{ $show_import_button ? 'true' : 'false' }}"
            :show-export-button="{{ $show_export_button ? 'true' : 'false' }}"
        />
    </div>

@endsection

@section('after_scripts')
    @vite('resources/js/initiatives.js')
@endsection
