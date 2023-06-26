@extends('backpack::layouts.top_left')

@section('after_styles')
    <!-- Google fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300" rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Raleway' rel='stylesheet' type='text/css'>
@endsection

@section('after_scripts')
    <!-- D3.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.6/d3.min.js" charset="utf-8"></script>
    @vite('js/radarChart.js')


    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>
    <script src="https://code.highcharts.com/modules/data.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jstat@latest/dist/jstat.min.js"></script>
    <script src="https://marketing-demo.s3-eu-west-1.amazonaws.com/violinFunction/processViolin.js"></script>
    {{--    <script src="{{asset('js/comparisonCharts.js')}}"></script>--}}

    <script>

        /* Radar chart design created by Nadieh Bremer - VisualCinnamon.com */

        //////////////////////////////////////////////////////////////
        //////////////////////// Set-Up //////////////////////////////
        //////////////////////////////////////////////////////////////

        var margin = {top: 100, right: 80, bottom: 100, left: 80},
            width = document.getElementById('radarChart').offsetWidth - margin.right - margin.left
        height = Math.min(width, window.innerHeight - margin.top - margin.bottom - 20);

        console.log('witdh', width);
        //////////////////////////////////////////////////////////////
        ////////////////////////// Data //////////////////////////////
        //////////////////////////////////////////////////////////////

        var data = [{!!$spiderData->values()->toJson() !!}];
        //////////////////////////////////////////////////////////////
        //////////////////// Draw the Chart //////////////////////////
        //////////////////////////////////////////////////////////////

        var color = d3.scale.ordinal()
            .range(["#EDC951", "#CC333F", "#00A0B0"]);

        var radarChartOptions = {
            w: width,
            h: height,
            margin: margin,
            maxValue: 2,
            levels: 4,
            roundStrokes: true,
            color: color
        };

        window.spiderChart("#radarChart", data, radarChartOptions);

        // principle comparison
        @foreach($yourPortfolio as $key => $values)
        let principle_{!! \Illuminate\Support\Str::slug($key, "_") !!} = {!! $values->toJson() !!}
            @endforeach

            let
        step = 0.5,
            precision = 0.1,
            violinWidth = 1;

        let violinData = processViolin(step, precision, violinWidth, @foreach($yourPortfolio as $key => $values)principle_{!! \Illuminate\Support\Str::slug($key, "_") !!}, @endforeach)

            xi = violinData.xiData;
        let stat = violinData.stat;
        @foreach($yourPortfolio as $key => $values)let violin_{!! \Illuminate\Support\Str::slug($key, "_") !!} = violinData.results[{{ $loop->index}}];
        @endforeach



        Highcharts.chart("yourPortfolio", {
            chart: {
                type: "areasplinerange",
                inverted: false
            },
            title: {
                text: ""
            },
            xAxis: {
                reversed: false,
                labels: {format: "{value}"},
                min: 0,
                max: 2,
            },
            yAxis: {
                title: {text: null},
                categories: [@foreach($yourPortfolio as $key => $values)"{!! $key !!}", @endforeach],
                startOnTick: false,
                endOnTick: false,
                gridLineWidth: 0
            },
            plotOptions: {
                series: {
                    marker: {
                        enabled: false
                    },
                    states: {
                        hover: {
                            enabled: false
                        }
                    },
                    pointStart: xi[0],
                    events: {
                        legendItemClick: function (e) {
                            e.preventDefault();
                        }
                    }
                }
            },
            legend: {
                enabled: false,
            },
            tooltip: {
                enabled: false,
            },

            series: [
                    @foreach($yourPortfolio as $key => $values)
                {
                    name: "{!! $key !!}", data: violin_{!! \Illuminate\Support\Str::slug($key, "_") !!}
                },
                @endforeach
            ]
        })

        // ALL Projects comparison
        @foreach($allPortfolio as $key => $values)
        let all_principle_{!! \Illuminate\Support\Str::slug($key, "_") !!} = {!! $values->toJson() !!}
            @endforeach

            let
        allViolinData = processViolin(step, precision, violinWidth, @foreach($allPortfolio as $key => $values)all_principle_{!! \Illuminate\Support\Str::slug($key, "_") !!}, @endforeach)

            xi = allViolinData.xiData;
        stat = allViolinData.stat;
        @foreach($allPortfolio as $key => $values)let all_violin_{!! \Illuminate\Support\Str::slug($key, "_") !!} = allViolinData.results[{{ $loop->index}}];
        @endforeach



        Highcharts.chart("allPortfolio", {
            chart: {
                type: "areasplinerange",
                inverted: false
            },
            title: {
                text: ""
            },
            xAxis: {
                reversed: false,
                labels: {format: "{value}"},
                min: 0,
                max: 2,
            },
            yAxis: {
                title: {text: null},
                categories: [@foreach($allPortfolio as $key => $values)"{!! $key !!}", @endforeach],
                startOnTick: false,
                endOnTick: false,
                gridLineWidth: 0
            },
            plotOptions: {
                series: {
                    marker: {
                        enabled: false
                    },
                    states: {
                        hover: {
                            enabled: false
                        }
                    },
                    pointStart: xi[0],
                    events: {
                        legendItemClick: function (e) {
                            e.preventDefault();
                        }
                    }
                }
            },
            legend: {
                enabled: false,
            },
            tooltip: {
                enabled: false,
            },

            series: [
                    @foreach($allPortfolio as $key => $values)
                {
                    name: "{!! $key !!}", data: all_violin_{!! \Illuminate\Support\Str::slug($key, "_") !!}
                },
                @endforeach
            ]
        })

    </script>

@endsection

@section('content')
    <h1 class="mt-4">Portfolio Analysis</h1>

    <form method="POST" action="/admin/generatePdf">
        @csrf
        <input type="submit" value="Generate PDF">
    </form>

    <h3 class="mt-4">{{ $organisation->name }}</h3>
    <ul class="nav nav-tabs" id="top-tabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-1" data-toggle="tab" data-target="#tab1Content" type="button">
                Overview
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-2" data-toggle="tab" data-target="#tab2Content" type="button">
                Principles - Score Distribution
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-3" data-toggle="tab" data-target="#tab3Content" type="button">
                Principles - Average Scroes
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-4" data-toggle="tab" data-target="#tab4Content" type="button">
                Redlines
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-5" data-toggle="tab" data-target="#tab5Content" type="button">
                Non-Applicable Principles
            </button>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="tab1Content">
            <div class="row">
                <div class="col-md-6 col-12">
                    @include('organisations.portfolio._keyIndicators')
                </div>
                <div class="col-md-6 col-12">
                    @include('organisations.portfolio._spiderChart')
                </div>
            </div>
        </div>
        <div class="tab-pane" id="tab2Content">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title"><b>Your portfolio</b></h4>
                        </div>
                        <div class="card-body">
                            <div id="yourPortfolio" style="height: 500px"></div>
                            <small>Only includes projects that passed the redlines</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title"><b>All projects in the platform</b></h4>
                        </div>
                        <div class="card-body">
                            <div id="allPortfolio" style="height: 500px"></div>
                            <small>Only includes projects that passed the redlines</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="tab3Content">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title"><b>Your portfolio</b>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th>Principle</th>
                                    <th>Score</th>
                                </tr>
                                @foreach($avgs as $key => $avg)
                                    <tr>
                                        <td>{{ $key }}</td>
                                        <td>{{ $avg }}</td>
                                    </tr>
                                @endforeach
                            </table>
                            <small>Only includes projects that passed the redlines</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title"><b>All projects in the platform</b></h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th>Principle</th>
                                    <th>Avg. Score</th>
                                </tr>

                                @foreach($allAvgs as $key => $avg)
                                    <tr>
                                        <td>{{ $key }}</td>
                                        <td>{{ $avg }}</td>
                                    </tr>
                                @endforeach
                            </table>
                            <small>Only includes projects that passed the redlines</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="tab4Content">
            @include('organisations.portfolio._redlines')
        </div>
        <div class="tab-pane" id="tab5Content">
            @include('organisations.portfolio.nonApplicablePrinciples')
        </div>
    </div>

@endsection
