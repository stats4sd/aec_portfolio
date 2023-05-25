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


    <form action="./confirm-to-leave" method="POST">

    @csrf

    <div class="col-12 col-xl-12 card">
        <div class="card-header d-flex align-items-flex-end justify-content-between">
            <div>
                <h2><b>Request to leave an institution</b></h2>
            </div>
        </div>
        <div class="card-body">
            <div class="container-fluid">
                <div class="row">
                    <div>
                        <p>Warning: After leaving this institution, you will not be able to login this data platform anymore.</p>

                        <p><b><input type="checkbox" name="remove_personal_data"> Also remove all of my personal data in this data platform</b></p>

                        <p>After submitting this request, you will be logout automatically.</p>

                        <input type="submit" value="Submit">
                    </div>
                </div>
            </div>
        </div>
    </div>

    </form>

</div>


@endsection


@section('after_scripts')

@endsection
