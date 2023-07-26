<!-- =================================================== -->
<!-- ========== Top menu items (ordered left) ========== -->
<!-- =================================================== -->

<ul class="nav navbar-nav d-md-down-none">

    @if (backpack_auth()->check())
        <!-- Topbar. Contains the left part -->
        @include(backpack_view('inc.topbar_left_content'))
    @endif

</ul>
<!-- ========== End of top menu left items ========== -->

<ul class="nav navbar-nav ml-auto mr-auto d-flex">
    <li class="nav-item mx-3 font-weight-bold"><a class="nav-link text-deep-green" href="{{ backpack_url('dashboard') }}">Dashboard</a>
        </li>
    <li class="nav-item mx-3 font-weight-bold"><a class="nav-link text-deep-green" href="{{ backpack_url('organisation/show') }}">My Institution</a>
        </li>
    <li class="nav-item mx-3 font-weight-bold"><a class="nav-link text-deep-green" href="{{ backpack_url('project') }}">Initiatives</a>
        </li>
</ul>


<!-- ========================================================= -->
<!-- ========= Top menu right items (ordered right) ========== -->
<!-- ========================================================= -->
<ul class="nav navbar-nav ml-auto @if(config('backpack.base.html_direction') == 'rtl') mr-0 @endif">
    @if (backpack_auth()->guest())
        <li class="nav-item"><a class="nav-link" href="{{ route('backpack.auth.login') }}">{{ trans('backpack::base.login') }}</a>
        </li>
        @if (config('backpack.base.registration_open'))
            <li class="nav-item"><a class="nav-link" href="{{ route('backpack.auth.register') }}">{{ trans('backpack::base.register') }}</a></li>
        @endif
    @else
        <!-- Topbar. Contains the right part -->
        @include(backpack_view('inc.topbar_right_content'))


        <li class="nav-item pr-4">
            <a class="nav-link d-flex justify-content-between align-items-center"
               href="https://www.youtube.com/playlist?list=PLK5PktXR1tmPMHKN6PnfY4jz9Cj_7L1E_"
               target="_blank"
            >
                <i class="la la-question-circle font-3xl pr-2"></i>
                <span>Video Guides</span>
            </a>
        </li>

        <li class="nav-item pr-4">
            <a class="nav-link d-flex justify-content-between align-items-center"
               href="{{ route('backpack.account.info') }}"
            >
                <i class="la la-user-circle font-3xl pr-2"></i>
                <span>My Account</span>
            </a>
        </li>J

        @if(\App\Models\Organisation::count() > 1)
        <li class="nav-item pr-4">
            <a class="nav-link d-flex justify-content-between align-items-center"
               href="{{ backpack_url('selected_organisation') }}"
            >
                <i class="la la-exclamation-circle font-3xl pr-2"></i>
                <span>Change Institution</span>
            </a>
        </li>
        @endif

        <!-- Logout button - tailored to use Laravel Breeze -->
        <li class="nav-item pr-4">
            <form method="POST" action={{ route('logout') }}>
            @csrf
            <button class="btn btn-link text-dark" type="submit">{{ trans('backpack::base.logout') }}</button>
            </form>
        </li>

    @endif
</ul>
<!-- ========== End of top menu right items ========== -->
