@extends('backpack::layouts.top_left')

@section('content')

<div class="container">

<div class="row pl-4 pt-4 w-100">
    <div class="col-12 col-xl-12 card">
        <h2>Please select an institution</h2>
    </div>
    <div class="col-12 col-xl-12 card">
    @foreach ($organisations as $organisation)
        <button type="button" class="btn btn-lg btn-block btn-outline-primary" id="{{ $organisation->id }}" onclick="submitForm(this);";>{{ $organisation->name }}</button>
    @endforeach
    </div>
</div>


<form name="selectedOrganisationForm" action="selected_organisation" method="get">
    <input type="hidden" name="organisationId">
</form>


</div>
@endsection

@section('after_scripts')
<script src="{{ mix('js/app.js') }}"></script>

<script>

function submitForm(organisationButton) {
    this.document.selectedOrganisationForm.organisationId.value = organisationButton.id;
    this.document.selectedOrganisationForm.submit();
}

</script>

@endsection
