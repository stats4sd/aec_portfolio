@extends('backpack::layouts.top_left')

@section('content')

    <div class="mt-16 container-fluid" id="dashboard">

        <div class="d-flex justify-content-between">
            <h1 class="font-weight-bold text-deep-green mb-4">{{ $organisation->name }} - Summary</h1>

            @if(Auth::user()->can('download project-level data'))
            <div>
                <a class="btn btn-info" href="{{ url('/admin/organisation/export') }}">Export All Initiative Data</a>
            </div>
            @endif
        </div>

        <v-app>
            <Suspense>
                <main-dashboard
                    :user="{{ Auth::user() }}"
                    :organisation="{{ $organisation->toJson() }}"
                    :regions="{{ $regions }}"
                    :countries="{{ $countries }}"
                />
            </Suspense>
        </v-app>

    </div>
@endsection

@section('after_scripts')

    @vite('resources/js/dashboard.js')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flot/4.2.3/jquery.canvaswrapper.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flot/4.2.3/jquery.colorhelpers.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flot/4.2.3/jquery.flot.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flot/4.2.3/jquery.flot.saturated.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flot/4.2.3/jquery.flot.browser.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flot/4.2.3/jquery.flot.drawSeries.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flot/4.2.3/jquery.flot.uiConstants.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flot/4.2.3/jquery.flot.axislabels.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flot/4.2.3/jquery.flot.legend.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flot/4.2.3/jquery.flot.stack.js"></script>

@endsection
