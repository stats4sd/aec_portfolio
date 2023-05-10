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
                <h2><b>Request to remove everything for institution</b></h2>
            </div>
        </div>
        <div class="card-body">
            <div class="container-fluid">
                <div class="row">
                    <div>
                        <p>After submitting this request, a confirmation email will be sent to site admin and you.</p>

                        <p>Considering the data removal is not reversable, there will be a 30 days cool down period after your submission.</p>

                        <p>A reminder email will be sent to site admin and you after 30 days. Please reply the email to site admin to confirm to remove everything for your institution.</p>

                        <p>When site admin received your confirmation email, the data removal process will be triggered by site admin.</p>

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
