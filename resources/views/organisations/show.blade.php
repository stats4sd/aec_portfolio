@extends('backpack::layouts.top_left')

@section('content')

    <div class="mt-16 container-fluid">

        <div class="w-100 mb-4">
            <h1 class="text-deep-green"><b>{{$organisation->name}} - Information</b></h1>
        </div>

        <ul class="nav nav-tabs mt-4" id="org-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a href="#members" class="nav-link px-8 org-tab" id="members-tab" data-toggle="tab" data-target="#members" type="button" role="tab" aria-controls="home" aria-selected="true">
                    <h5 class="text-uppercase m-0 p-0">Users</h5>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="#portfolios" class="nav-link px-8 org-tab" id="portfolios-tab" data-toggle="tab" data-target="#portfolios" type="button" role="tab" aria-controls="home" aria-selected="true">
                    <h5 class="text-uppercase m-0 p-0">Portfolios</h5>
                </a>
            </li>
            @if($organisation->has_additional_criteria)
                <li class="nav-item" role="presentation">
                    <a href="#additional-criteria" class="nav-link px-8 org-tab" id="additional-criteria-tab" data-toggle="tab" data-target="#additional-criteria" type="button" role="tab" aria-controls="home" aria-selected="true">
                        <h5 class="text-uppercase m-0 p-0">Additional Assessment Criteria</h5>
                    </a>
                </li>
            @endif
            <li class="nav-item" role="presentation">
                <a href="#settings" class="nav-link px-8 org-tab active" id="settings-tab" data-toggle="tab" data-target="#settings" type="button" role="tab" aria-controls="settings" aria-selected="false"><h5 class="text-uppercase m-0 p-0">Settings</h5>
                </a>
            </li>
        </ul>
        <div class="tab-content" id="org-tabs-content">
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


    <script>
        $(document).ready(() => {

            let url = location.href.replace(/\/$/, "");

            if (location.hash) {
                const hash = url.split("#");
                $('#org-tabs a[href="#' + hash[1] + '"]').tab("show");
                url = location.href.replace(/\/#/, "#");
                history.replaceState(null, null, url);
                setTimeout(() => {
                    $(window).scrollTop(0);
                }, 400);
            } else {
                $('#org-tabs a[href="#portfolios"]').tab("show");
                url = location.href.replace(/\/#/, "#");
                history.replaceState(null, null, url);
                setTimeout(() => {
                    $(window).scrollTop(0);
                }, 400);
            }

            $('a.org-tab').on("click", function () {
                let newUrl;
                const hash = $(this).attr("href");
                if (hash == "#members") {
                    newUrl = url.split("#")[0];
                } else {
                    newUrl = url.split("#")[0] + hash;
                }
                newUrl += "/";
                history.replaceState(null, null, newUrl);
            });

            // enable hover tooltips
            $('[data-toggle="popover"]').popover({
                'trigger': 'hover click',
            })
        });
    </script>

@endsection
