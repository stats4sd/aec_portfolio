@extends('backpack::layouts.top_left')

@section('content')

    <div class="container mt-8">

        <div class="w-100 card">
            @if($organisations->count() > 1)
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
            @else
                <div class="card-header">
                    <div class="alert alert-info show">
                        It looks like your account is not associated with any institution. If this is incorrect, please contact the person who invited you to this platform to check that they have added you to the correct institution.
                    </div>
                </div>
            @endif
        </div>
    </div>


    <form name="selectedOrganisationForm" action="{{ backpack_url('selected_organisation') }}" method="POST">
        @csrf
        @method('POST')
        <input type="hidden" name="organisationId">
        <input type="hidden" name="redirect" value="{{ Session::get('redirect') }}">
    </form>

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
