@extends(backpack_view('blank'))

@section('content')

    @if(! $organisation->agreement_signed_at && $organisation->admins->contains(auth()->user()))
        <div class="alert alert-warning show text-dark my-3">Before continuing, please check and agree to the data sharing agreement, available on the
            <a href="{{ url('admin/organisation/show#settings') }}">My Institution page - Settings tab</a>.
            <br/><br/>
            Once you have signed the agreement, you will be able to add and assess your institution's initiatives.
        </div>
    @elseif(! $organisation->agreement_signed_at && !$organisation->admins->contains(auth()->user()))
        <div class="alert alert-warning text-dark my-3">This institution has not yet digitally signed the data sharing agreement. An institution admin should update the settings in order to enable the ability to add and assess initiatives using the tool.</div>
    @endif

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
            :enable-delete-button="{{ $enable_delete_button ? 'true' : 'false'}}"
            :enable-assess-button="{{ $enable_assess_button ? 'true' : 'false' }}"
        />
    </div>

@endsection

@section('after_scripts')
    @vite('resources/js/initiatives.js')
@endsection
