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
                    <h5 class="text-uppercase m-0 p-0">User Feedback</h5>
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
                    <p>We have a series of short videos to guide new users through the setup process. You can watch the videos on the
                        <a href="#resources" onclick="switchtab('resources')">Training Resources tab</a> on this page.
                    </p>

                    <hr/>

                    <h3 class="pt-8">2. Institutional Support</h3>
                    @if(Auth::user()->hasAnyRole(['Institutional Assessor', 'Institutional Member', 'Institutional Admin']))
                        @if(Auth::user()->hasAnyRole(['Institutional Assessor', 'Institutional Member']))
                            To get support within your own institution, you may contact an administrator for your institution:
                        @elseif(Auth::user()->hasRole('Institutional Admin'))
                            As an institutional administrator, you have full access to your institution's data, and can field support requests from other members. If you have questions about the use of the tool, the other resources on this page may help. For reference, your institution's administrators are:
                        @endif

                        <ul class="list-group mt-4">
                            @foreach(\App\Models\Organisation::find(Session::get('selectedOrganisationId'))->admins as $admin)
                                <li class="list-group-item d-flex">
                                    <span class="w-25 text-right mr-4" style="min-width: 150px">{{ $admin->name }}:</span>
                                    <span><a href="mailto:{{ $admin->email }}">{{ $admin->email }}</a></span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        As a site manager or administrator, you can access and provide support to any of the institutions registered on the platform. For institutional users, this section lists the Institutional Administrator users so they can get in touch for internal support.
                    @endif

                    <hr/>

                    <h3 class="pt-8">3. User Feedback</h3>
                    <p>If you encounter any technical issues while using the tool, please contact us. Our site managers may be able to help, and bug reports will be forwarded onto our technical team. See the <a href="#feedback" onclick="switchtab('feedback')">User Feedback tab</a> for details.</p>
                </div>
            </div>
            <div class="tab-pane fade" id="resources" role="tabpanel" aria-labelledby="resources-tab">
                <div class="card-header">
                    <h2>Online Resources</h2>
                    <p class="font-lg">We have developed a series of training videos for new users, available below.</p>
                </div>

                <div class="card-body w-75">

                    <h3>Video 1: Registration and User Management</h3>
                    <p class="mb-4">This video provides an introduction to the platform and explains how to register as a new user. It also provides an overview of the different user roles available, so may be useful to help decide what roles to give users within your own institution.</p>
                    <iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/H982800IwwY" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>

                    <hr/>

                    <h3>Video 2: Creating Initiatives</h3>
                    <p class="mb-4">This video demonstrates the process of adding new initiatives to the platform.</p>
                    <iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/pnKAfYKPvOQ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>

                    <hr/>
                    <h3>Video 3: Assessing Initiatives </h3>
                    <p class="mb-4">This video demonstrates the process of assessing your initiatives using the tool.</p>
                    <iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/7ph03W87WQY" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                </div>
            </div>

            <div class="tab-pane fade" id="feedback" role="tabpanel" aria-labelledby="feedback-tab">
                <div id="user-feedback-form">
                    <user-feedback-form
                        :user="{{ Auth::user()->toJson()}}"
                        post-route="{{ url('admin/user-feedback') }}"
                        :user-feedback-types="{{ \App\Models\UserFeedbackType::all()->toJson() }}"
                    >
                </div>

        </div>

    </div>

@endsection


@section('after_scripts')
    @vite(['resources/js/app.js', 'resources/js/user-feedback.js'])


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


        function switchtab(tabname) {

            var tabid = tabname + '-tab'
            $('#'+tabid).tab('show')
        }
    </script>

@endsection
