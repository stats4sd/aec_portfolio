@extends('backpack::layouts.top_left')

@section('content')

    <div class="mt-16 container-fluid">
        @if(! $organisation->agreement_signed_at && $organisation->admins->contains(auth()->user()))
        <div class="alert alert-warning show text-dark my-3">Before continuing, please check and agree to the data sharing agreement, available on the Settings tab.</div>
        @elseif(! $organisation->agreement_signed_at && !$organisation->admins->contains(auth()->user()))
        <div class="alert alert-warning text-dark my-3">This institution has not yet agreed to the data sharing agreement. An institution admin should update the settings.</div>
        @endif

        <div class="w-100 mb-4 d-flex align-items-center">
            <h1 class="text-deep-green mb-0"><b>{{$organisation->name}} - Information</b></h1>
            <x-help-text-link class="font-2xl" location="My Institution - page title"/>
        </div>

        <x-help-text-entry location="My Institution - page title"/>

        <ul class="nav nav-tabs mt-4" id="org-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a href="#members" class="nav-link px-8 org-tab" id="members-tab" data-toggle="tab"
                   data-target="#members" type="button" role="tab" aria-controls="home" aria-selected="true">
                    <h5 class="text-uppercase m-0 p-0">Users</h5>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="#portfolios" class="nav-link px-8 org-tab" id="portfolios-tab" data-toggle="tab"
                   data-target="#portfolios" type="button" role="tab" aria-controls="home" aria-selected="true">
                    <h5 class="text-uppercase m-0 p-0">Portfolios</h5>
                </a>
            </li>
            @if($organisation->has_additional_criteria)
                <li class="nav-item" role="presentation">
                    <a href="#additional-criteria" class="nav-link px-8 org-tab" id="additional-criteria-tab"
                       data-toggle="tab" data-target="#additional-criteria" type="button" role="tab"
                       aria-controls="home" aria-selected="true">
                        <h5 class="text-uppercase m-0 p-0">Additional Assessment Criteria</h5>
                    </a>
                </li>
            @endif
            <li class="nav-item" role="presentation">
                <a href="#settings" class="nav-link px-8 org-tab active" id="settings-tab" data-toggle="tab"
                   data-target="#settings" type="button" role="tab" aria-controls="settings" aria-selected="false"><h5
                        class="text-uppercase m-0 p-0">Settings</h5>
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
                <div class="tab-pane fade" id="additional-criteria" role="tabpanel"
                     aria-labelledby="additional-criteria-tab-tab">
                    @include('organisations.additional-criteria')
                </div>
            @endif
            <div class="tab-pane fade show active" role="tabpanel" aria-labelledby="settings-tab" id="settings">
                <div id="org-settings">
                    <Suspense>
                        <institution-settings
                            :init-institution="{{ $organisation }}"
                            :institution-types="{{ $institutionTypes->toJson() }}"
                            :geographic-reaches="{{ json_encode($geographicReaches) }}"
                            :countries="{{ $countries->toJson() }}"
                            update-route="{{ route('organisation.self.update') }}"
                            :user="{{ Auth::user()->withPermissionNames() }}"
                        />
                    </Suspense>
                </div>
            </div>
        </div>


    </div>
@endsection

@section('after_scripts')
    @vite(['resources/js/app.js', 'resources/js/institutions.js'])


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

                let tab = "{{ $tab }}"

                // get from session storage
                $('#org-tabs a[href="#'+tab+'"]').tab("show");
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

                // update session storage
                axios.post('/admin/organisation/store-tab', {tab: hash})
            });

            // enable hover tooltips
            $('[data-toggle="popover"]').popover({
                'trigger': 'hover click',
            })
        });
    </script>

@endsection
