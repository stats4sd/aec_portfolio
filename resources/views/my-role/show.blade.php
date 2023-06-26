@extends('backpack::layouts.top_left')

@section('content')


<div class="row pl-4 pt-4 w-100">
    <div class="col-12 col-xl-12 card">
        <div class="card-header d-flex align-items-flex-end justify-content-between">
            <div>
                <h1><b>{{ $organisation->name }}</b></h1>
            </div>
        </div>
        <div class="card-body">
            <div class="container-fluid">
                <div class="row">
                    <div>
                        <!-- suppose one account should link to one role only -->
                        <h3><b>{{ auth()->user()->name }} ({{ auth()->user()->getRoleNames()[0] }})</b></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-12 card">
        <div class="card-header d-flex align-items-flex-end justify-content-between">
            <div>
                <b><a href="./my-role/request-to-leave">Request to leave an institution</a></b>
            </div>
        </div>
    </div>

    @if ( auth()->user()?->hasRole('Institutional Admin') )
    <div class="col-12 col-xl-12 card">
        <div class="card-header d-flex align-items-flex-end justify-content-between">
            <div>
                <b><a href="./my-role/request-to-remove-everything">Request to remove everything for institution</a></b>
            </div>
        </div>
    </div>
    @endif

</div>



@endsection


@section('after_scripts')

@endsection
