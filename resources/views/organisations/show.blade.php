@extends('backpack::layouts.top_left')

@section('content')

    <div class="container">

        <div class=" pt-16 w-100">
            <h1 class="text-deep-green"><b>{{$organisation->name}}</b></h1>
            <p class="alert alert-info show">
                Review and manage the institutional settings and members. If you are not an institutional administrator, you can identify the administrators on this page in order to contact them with any requests.
            </p>
        </div>

        <ul class="nav nav-tabs mt-4" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link px-8" id="members-tab" data-toggle="tab" data-target="#members" type="button" role="tab" aria-controls="home" aria-selected="true">
                    <h5 class="text-uppercase m-0 p-0">Users</h5>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link px-8" id="portfolios-tab" data-toggle="tab" data-target="#portfolios" type="button" role="tab" aria-controls="home" aria-selected="true">
                    <h5 class="text-uppercase m-0 p-0">Portfolios</h5>
                </button>
            </li>
            @if($organisation->has_additional_criteria)
            <li class="nav-item" role="presentation">
                <button class="nav-link px-8" id="additional-criteria-tab" data-toggle="tab" data-target="#additional-criteria" type="button" role="tab" aria-controls="home" aria-selected="true">
                    <h5 class="text-uppercase m-0 p-0">Additional Assessment Criteria</h5>
                </button>
            </li>
            @endif
            <li class="nav-item" role="presentation">
                <button class="nav-link px-8 active" id="settings-tab" data-toggle="tab" data-target="#settings" type="button" role="tab" aria-controls="settings" aria-selected="false"><h5 class="text-uppercase m-0 p-0">Settings</h5>
                </button>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade" id="members" role="tabpanel" aria-labelledby="members-tab">
                @include('organisations.members')
            </div>
            <div class="tab-pane fade" id="portfolios" role="tabpanel" aria-labelledby="portfolios-tab">
                @include('organisations.portfolios')
            </div>
            @if($organisation->has_additional_criteria)
            <div class="tab-pane fade" id="additional-criteria" role="tabpanel" aria-labelledby="additional-criteria-tab-tab">
                @include('organisations.additional-criteria')
            </div>
            @endif
            <div class="tab-pane fade show active" id="settings" role="tabpanel" aria-labelledby="settings-tab">
                @include('organisations.settings')
            </div>
        </div>


    </div>
@endsection

@section('after_scripts')
    @vite('resources/js/app.js')
@endsection
