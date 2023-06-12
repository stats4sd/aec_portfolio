@extends('backpack::layouts.top_left')

@section('content')

    <div class="container mt-8">

        <div class="w-100 card">
            <div class="card-header">
                <h2>Please select an institution</h2>
                <p>Your account has access to multiple institutions. To view information about projects and portfolios, please select which institution to view.</p>
            </div>
            <div class="card-body">
                <div class="col-12 col-xl-12">
                    @foreach ($organisations as $organisation)
                        <button type="button" class="btn btn-lg btn-block btn-outline-primary" id="{{ $organisation->id }}" onclick="submitForm(this);" ;>{{ $organisation->name }}</button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>


    <form name="selectedOrganisationForm" action="{{ backpack_url('selected_organisation') }}" method="POST">
        @csrf
        @method('POST')
        <input type="hidden" name="organisationId">
        <input type="hidden" name="redirect" value="{{ Session::get('redirect') }}">
    </form>


    </div>
@endsection

@section('after_scripts')
    @vite('resources/js/app.js')

    <script>

        function submitForm(organisationButton) {
            this.document.selectedOrganisationForm.organisationId.value = organisationButton.id;
            this.document.selectedOrganisationForm.submit();
        }

    </script>

@endsection
