@extends('backpack::layouts.top_left')

@section('content')

<div class="container">

<div class="row pl-4 pt-4 w-100">

    <div class="col-12 col-xl-12 card">
        <h3>TODO: {{ $level }}-level dashboard for {{ $organisation->name }}</h3>
    </div>
    

    @if(($level == 'institution') && (Auth::user()->can('download dashboard summary data')))
        <div class="col-12 col-xl-12 card">
            <a href="#">Download institution-level dashboard summary data</a>
        </div>
    @endif

    @if(($level == 'portfolio') && (Auth::user()->can('download dashboard summary data')))
        <div class="col-12 col-xl-12 card">
            <a href="#">Download portfolio-level dashboard summary data</a>
        </div>
    @endif

    @if(($level == 'initiative') && (Auth::user()->can('download dashboard summary data')))
        <div class="col-12 col-xl-12 card">
            <a href="#">Download initiative-level dashboard summary data</a>
        </div>
    @endif

    
    @if(($level == 'institution') && (Auth::user()->can('download institution-level data')))
        <div class="col-12 col-xl-12 card">
            <a href="#">Download institution-level data</a>
        </div>
    @endif

    @if(($level == 'portfolio') && (Auth::user()->can('download portfolio-level data')))
        <div class="col-12 col-xl-12 card">
            <a href="#">Download portfolio-level data</a>
        </div>
    @endif

    @if(($level == 'initiative') && (Auth::user()->can('download project-level data')))
        <div class="col-12 col-xl-12 card">
            <a href="#">Download initiative-level data</a>
        </div>
    @endif

</div>


</div>
@endsection

@section('after_scripts')
<script src="{{ mix('js/app.js') }}"></script>

@endsection
