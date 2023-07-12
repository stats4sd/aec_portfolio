@extends('backpack::layouts.top_left')

@section('content')

    <a href="{{backpack_url('project')}}" class="btn btn-link no-print">Back to initiatives list</a>

    <div class="container mt-4">

        <h1>Initiative Review Page</h1>
        <h2>{{ $entry->organisation->name }} - {{ $entry->name }}</h2>

        <form method="POST" action="/admin/generatePdf">
            @csrf
            <input class="no-print" type="submit" value="Generate PDF">
        </form>

        <div class="row mt-3 mb-4">
            <div class="col-12 col-lg-6 print-4 pt-4 mt-4 d-flex align-items-center">

                <table class="table table-borderless">
                    <tr>
                        <td class="text-right pr-4 mr-2">Project Code:</td>
                        <td>{{ $entry->code }}</td>
                    </tr>
                    <tr>
                        <td class="text-right pr-4 mr-2">Budget:</td>
                        <td>{{ $entry->currency }} {{ $entry->budget }}</td>
                    </tr>
                    <tr>
                        <td class="text-right pr-4 mr-2">Budget (in {{ $entry->organisation->currency }}):</td>
                        <td>{{ $entry->organisation->currency }} {{ $entry->budget_org }}</td>
                    </tr>
                    <tr>
                        <td class="text-right pr-4 mr-2">Start Date:</td>
                        <td>{{ $entry->start_date->toDateString() }}</td>
                    </tr>
                    @if($entry->end_date)
                        <tr>
                            <td class="text-right pr-4 mr-2">End Date:</td>
                            <td>{{ $entry->end_date->toDateString() }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="text-right pr-4 mr-2">Geographic Reach:</td>
                        <td>{{ $entry->geographic_reach }}</td>
                    </tr>
                    @if($entry->continents)
                        <tr>
                            <td class="text-right pr-4 mr-2">Continent/s:</td>
                            <td>{{ $entry->continents->pluck('name')->join(', ') }}</td>
                        </tr>
                    @endif
                    @if($entry->regions)
                        <tr>
                            <td class="text-right pr-4 mr-2">Region/s:</td>
                            <td>{{ $entry->regions->pluck('name')->join(', ') }}</td>
                        </tr>
                    @endif
                    @if($entry->countries)
                        <tr>
                            <td class="text-right pr-4 mr-2">Country/ies:</td>
                            <td>{{ $entry->countries->pluck('name')->join(', ') }}</td>
                        </tr>
                    @endif
                    @if($entry->sub_regions)
                        <tr>
                            <td class="text-right pr-4 mr-2">Sub-country Region(s):</td>
                            <td>{{ $entry->sub_regions }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="text-right pr-4 mr-2">Status:</td>
                        <td>{{ $entry->latest_assessment_status }}</td>
                    </tr>

                    <tr>
                        <td class="text-right pr-4 mr-2">Ratings Total:</td>
                        @if($entry->latest_assessment->principle_status === \App\Enums\AssessmentStatus::Complete->value)
                            <td>{{ $entry->latest_assessment->total }} / {{ $entry->latest_assessment->total_possible }}</td>
                        @else
                            <td class="text-secondary">~~ Assessment not yet completed ~~</td>
                        @endif
                    </tr>
                    <tr>
                        <td class="text-right pr-4 mr-2">Overall Score:</td>
                        @if($entry->latest_assessment->principle_status === \App\Enums\AssessmentStatus::Complete->value)
                            <td><span class="font-weight-bold">{{ $entry->latest_assessment->overall_score }} % </span>
                                <br/>
                                <span class="text-sm text-secondary">Calculated based on {{ $entry->latest_assessment->principleAssessments()->where('is_na', 0)->count() }} / 13 relevant principles.</span>
                            </td>
                        @else
                            <td class="text-secondary">~~ Assessment not yet completed ~~</td>
                        @endif
                    </tr>
                </table>
            </div>

            <div class="col-12 col-lg-6 print-8">
                <div id="radarChart"></div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <table class="table table-borderless table-responsive">
                    <tr>
                        <th>Principle</th>
                        <th>Score</th>
                        <th>Comments</th>
                        <th>Shared Examples/Indicators</th>
                        <th>Custom Examples/Indicators</th>
                    </tr>

                    @php
                        $principleAssessments = $entry->latest_assessment->principleAssessments;
                    @endphp

                    @foreach(\App\Models\Principle::all() as $principle)

                        @php
                            $principleAssessment = $principleAssessments->where('principle_id', $principle->id)->first();
                        @endphp
                        <tr>
                            <td>{{ $principle->name }}</td>
                            <td> {{ $principleAssessment->is_na ? "NA" : $principleAssessment->rating }}</td>
                            <td> {{ $principleAssessment->is_na ? "-" : $principleAssessment->rating_comment }}</td>
                            <td>
                                @if($principleAssessment->scoreTags()->count() > 0)
                                    <button class="btn btn-link" type="button" data-toggle="modal"
                                            data-target="#modal-shared-{{$principle->id}}">
                                        {{ $principleAssessment->scoreTags()->count() }} selected
                                    </button>
                                @else
                                    <span class="btn">0 selected</span>
                                @endif
                            </td>
                            <td>
                                @if($principleAssessment->customScoreTags()->count() > 0)
                                    <button class="btn btn-link" type="button" data-toggle="modal"
                                            data-target="#modal-custom-shared-{{$principle->id}}">
                                        {{ $principleAssessment->customScoreTags()->count() }} added
                                    </button>
                                @else
                                    <span class="btn">0 added</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach

                </table>
            </div>
        </div>
    </div>

    @foreach(\App\Models\Principle::all() as $principle)

        @php
            $principleProjectModal = $principleAssessments->where('principle_id', $principle->id)->first();
        @endphp


        {{-- Modal for Shared  Examples / Indicators--}}
        <div class="modal fade" tabindex="-1" role="dialog" id="modal-shared-{{$principle->id}}">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Examples / Indicators for {{ $principle->name }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info show">
                            This is the list of examples / indicators that help explain the rating given
                            for {{ $principle->name }}.
                        </div>
                        <table class="table table-borderless">
                            <tr>
                                <th>Example / Indicator</th>
                            </tr>
                            @foreach($principleProjectModal->scoreTags as $scoreTag)
                                <tr>
                                    <td>{{ $scoreTag->name }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal for CUSTOM  Examples / Indicators--}}
        <div class="modal fade" tabindex="-1" role="dialog" id="modal-custom-shared-{{$principle->id}}">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Examples / Indicators for {{ $principle->name }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info show">
                            This is the list of custom examples / indicators added for this project to help explain the
                            rating given for {{ $principle->name }}.
                        </div>
                        <table class="table  table-borderless">
                            <tr>
                                <th>Example / Indicator</th>
                                <th>Description</th>
                            </tr>
                            @foreach($principleProjectModal->customScoreTags as $scoreTag)
                                <tr>
                                    <td>{{ $scoreTag->name }}</td>
                                    <td>{{ $scoreTag->description }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

@endsection

@section('after_scripts')
    @vite('resources/js/app.js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.6/d3.min.js" charset="utf-8"></script>

    @vite('resources/js/radarChart.js')
    <script type="module">

        /* Radar chart design created by Nadieh Bremer - VisualCinnamon.com */

        //////////////////////////////////////////////////////////////
        //////////////////////// Set-Up //////////////////////////////
        //////////////////////////////////////////////////////////////

        var margin = {top: 100, right: 100, bottom: 100, left: 100},
            width = document.getElementById('radarChart').offsetWidth - margin.right - margin.left,
            height = Math.min(width, window.innerHeight - margin.top - margin.bottom - 20);


        // set minimum width;
        if(width < 250) {
            width = 250;
            height = 250;
        }
        //////////////////////////////////////////////////////////////
        ////////////////////////// Data //////////////////////////////
        //////////////////////////////////////////////////////////////

        var data = [{!! $spiderData->values()->toJson() !!}];
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
    </script>
@endsection
