@extends('backpack::layouts.top_left')

@section('content')

<div class="container">

<div class="row pl-4 pt-4 w-100">

    <div class="col-12 col-xl-12 card">
        <h3>TODO: Dashboard for {{ $organisation->name }}</h3>
    </div>
    

    <div id="dashboard">
        <dashboard :user="{{ Auth::user() }}" :organisation="{{ $organisation->toJson() }}" />
    </div>

    @if(Auth::user()->can('download dashboard summary data'))
        <div class="col-12 col-xl-12 card">
            <a href="#">Download dashboard summary data</a>
        </div>
    @endif

    
    @if(Auth::user()->can('download portfolio-level data'))
        <div class="col-12 col-xl-12 card">
            <a href="#">Download data</a>
        </div>
    @endif

</div>


</div>
@endsection

@section('after_scripts')
<script src="{{ mix('js/app.js') }}"></script>

@endsection
