@extends('backpack::layouts.top_left')

@section('content')

<div class="container">

<div class="row pl-4 pt-4 w-100">
    <div class="col-12 col-xl-12 card">
        <h3>TODO: Institution-level dashboard for {{ $organisation->name }}</h3>
    </div>
</div>


</div>
@endsection

@section('after_scripts')
<script src="{{ mix('js/app.js') }}"></script>

@endsection
