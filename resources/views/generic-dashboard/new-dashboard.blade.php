@extends('backpack::layouts.top_left')


@section('content')

    <style type="text/css">

        .demo-container-1 {
            box-sizing: border-box;
            width: 594px;
            height: 450px;
            padding: 20px 15px 15px 15px;
            margin: 15px auto 30px auto;
        }

        .demo-container-2 {
            box-sizing: border-box;
            width: 350px;
            height: 450px;
            padding: 20px 15px 15px 15px;
            margin: 15px auto 30px auto;
        }

        .demo-placeholder {
            width: 100%;
            height: 100%;
            font-size: 14px;
        }

        .legend {
            display: block;
            -webkit-padding-start: 2px;
            -webkit-padding-end: 2px;
            border-width: initial;
            border-style: none;
            border-color: initial;
            border-image: initial;
            padding-left: 10px;
            padding-right: 10px;
            padding-top: 15px;
            padding-bottom: 10px;
            font-size: 14px;
        }

        .legendLayer .background {
            fill: rgba(255, 255, 255, 0.65);
            stroke: rgba(0, 0, 0, 0.85);
            stroke-width: 1;
        }

        .tickLabel {
            font-size: 90%
        }

    </style>


    <div class="container" id="dashboard">

        <div class="row pl-4 pt-4 w-100">

            <div class="col-12 col-xl-12 card">
                <div class="card-header">
                    <h3>Dashboard for {{ $organisation->name }}</h3>
                </div>
                <div class="card-body">
                    <!-- dashboard Vue component -->
                    <dashboard :user="{{ Auth::user() }}" :organisation="{{ $organisation->toJson() }}" :portfolios="{{ $portfolios->toJson() }}" :regions="{{ $regions->toJson() }}" :countries="{{ $countries->toJson() }}"/>
                </div>

                <div class="card-footer">

                    @if(Auth::user()->can('download dashboard summary data'))
                        <a class="btn btn-primary" href="#">Download dashboard summary data</a>

                    @endif

                    @if(Auth::user()->can('download portfolio-level data'))

                        <a class="btn btn-primary" href="#">Download data</a>
                    @endif

                </div>
            </div>
        </div>

    </div>


    </div>
@endsection

@section('after_scripts')
    @vite('resources/js/app.js')

        @vite('resources/js/dashboard-temp.js')


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
