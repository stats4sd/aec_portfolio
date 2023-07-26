@extends(backpack_view('blank'))

@section('content')

    <div class="container-fluid mt-16" id="initiativesListPage">
        <initiatives-list
            :organisation="{{ $organisation }}"
            :initial-initiatives="{{ $projects->toJson() }}"
            :has-additional-assessment="{{ $has_additional_assessment ? 'true' : 'false' }}"
            :show-add-button="{{ $show_add_button ? 'true' : 'false' }}"
            :show-import-button="{{ $show_import_button ? 'true' : 'false' }}"
            :show-export-button="{{ $show_export_button ? 'true' : 'false' }}"
            :enable-edit-button="{{ $enable_edit_button ? 'true' : 'false' }}"
            :enable-show-button="{{ $enable_show_button ? 'true' : 'false' }}"
            :enable-assess-button="{{ $enable_assess_button ? 'true' : 'false' }}"
            :settings="{{ json_encode($settings) }}"
        />
    </div>

@endsection

@section('after_scripts')
    @vite('resources/js/initiatives.js')
@endsection
