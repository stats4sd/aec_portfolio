@extends('backpack::layouts.top_left')

@section('content')

    <div class="mt-16 container-fluid">

        <div class="w-100 mb-4">
            <h1 class="text-deep-green">
                <b>Support and User Feedback</b>
            </h1>
        </div>

        <ul class="nav nav-tabs mt-4" id="org-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a href="#support" class="nav-link px-8 org-tab" id="support-tab" data-toggle="tab"
                   data-target="#support" type="button" role="tab" aria-controls="home" aria-selected="true">
                    <h5 class="text-uppercase m-0 p-0">Getting Support</h5>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="#resources" class="nav-link px-8 org-tab" id="resources-tab" data-toggle="tab"
                   data-target="#resources" type="button" role="tab" aria-controls="home" aria-selected="true">
                    <h5 class="text-uppercase m-0 p-0">Training Resources</h5>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="#feedback" class="nav-link px-8 org-tab" id="feedback-tab" data-toggle="tab"
                   data-target="#feedback" type="button" role="tab" aria-controls="home" aria-selected="true">
                    <h5 class="text-uppercase m-0 p-0">User Support</h5>
                </a>
            </li>
        </ul>

        <div class="tab-content" id="org-tabs-content">
            <div class="tab-pane fade" id="support" role="tabpanel" aria-labelledby="support-tab">

                <div class="card-header">
                    <h2>Getting Support to use the tool</h2>
                    <p class="font-lg">There are a number of ways you can get support in the use of this tool. This page provides some resources and points of contact, and also allows you to share feedback about the tool to the site management team.</p>
                </div>

                <div class="card-body font-lg">

                    <h3 class="pt-8">1. Training Resources</h3>
                    <p>We have a series of short videos to guide new users through the setup process. You can watch the videos on the <a href="#resources">Training Resources tab</a> on this page.</p>

                    <hr/>

                    <h3 class="pt-8">2. Institutional Support</h3>
                    @if(\Illuminate\Support\Facades\Auth::user()->hasAnyRole(['Institutional Assessor', 'Institutional Member']))
                        To get support within your own institution, you may contact an administrator for your institution:

                        <ul>
                            @foreach(Organisation::find(Session::get('selectedOrganisationId'))->admins as $admin)
                                <li>{{ $admin->name }}: {{ $admin->email }}</li>
                            @endforeach
                        </ul>
                    @elseif(Auth::user()->hasRole('Institutional Admin'))
                        As an institutional administrator, you have full access to your institution's data, and can field support requests from other members. If you have questions about the use of the tool, the other resources on this page may help
                    @else
                        As a site manager or administrator, you can access and provide support to any of the institutions registered on the platform.
                    @endif
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
                $('#org-tabs a[href="#support"]').tab("show");
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
