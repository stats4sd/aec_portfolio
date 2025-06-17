@extends('backpack::layouts.top_left')

@section('content')

    <a href="{{ backpack_url('project') }}" class="btn btn-link no-print">Back to initiatives list</a>

    <div class="container mt-4">

        <h1>{{ $entry->organisation->name }} - {{ \Illuminate\Support\Str::title($entry->name) }}</h1>
        @if (count($entry->assessments) > 1)
            <h4>INITIATIVE SUMMARY
                <span class="text-bright-green no-print">- CURRENT ASSESSMENT (Assessment
                    {{ count($entry->assessments) }})</span>
            </h4>
        @else
            <h4 class="text-uppercase">Initiative Summary</h4>
        @endif
        <form method="POST" action="{{ backpack_url("project/$entry->id/generate-pdf") }}">
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
                        <td>{{ $entry->currency }} {{ $entry->displayBudget }}</td>
                    </tr>
                    @if ($entry->currency !== $entry->organisation->currency)
                        <tr>
                            <td class="text-right pr-4 mr-2">Budget (in {{ $entry->organisation->currency }}):</td>
                            <td>{{ $entry->organisation->currency }} {{ $entry->displayBudgetOrg }}</td>
                        </tr>
                    @endif
                    @if ($entry->currency !== 'EUR')
                        <tr>
                            <td class="text-right pr-4 mr-2">Budget (in EUR):</td>
                            <td>EUR {{ $entry->displayBudgetEur }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="text-right pr-4 mr-2">Start Date:</td>
                        <td>{{ $entry->start_date->toDateString() }}</td>
                    </tr>
                    @if ($entry->end_date)
                        <tr>
                            <td class="text-right pr-4 mr-2">End Date:</td>
                            <td>{{ $entry->end_date->toDateString() }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="text-right pr-4 mr-2">Geographic Reach:</td>
                        <td>{{ $entry->geographic_reach }}</td>
                    </tr>
                    @if ($entry->continents)
                        <tr>
                            <td class="text-right pr-4 mr-2">Continent/s:</td>
                            <td>{{ $entry->continents->pluck('name')->join(', ') }}</td>
                        </tr>
                    @endif
                    @if ($entry->regions)
                        <tr>
                            <td class="text-right pr-4 mr-2">Region/s:</td>
                            <td>{{ $entry->regions->pluck('name')->join(', ') }}</td>
                        </tr>
                    @endif
                    @if ($entry->countries)
                        <tr>
                            <td class="text-right pr-4 mr-2">Country/ies:</td>
                            <td>{{ $entry->countries->pluck('name')->join(', ') }}</td>
                        </tr>
                    @endif
                    @if ($entry->sub_regions)
                        <tr>
                            <td class="text-right pr-4 mr-2">Sub-country Region(s):</td>
                            <td>{{ $entry->sub_regions }}</td>
                        </tr>
                    @endif

                </table>
            </div>

            <div class="col-12 col-lg-6 print-8 d-flex justify-content-top align-items-center flex-column">
                @if (
                    $entry->latest_assessment->principle_status === \App\Enums\AssessmentStatus::Complete->value &&
                        $entry->latest_assessment->redline_status !== \App\Enums\AssessmentStatus::Failed->value)
                    <div id="radarChart" class="w-100"></div>

                    @if ($entry->latest_assessment->principleAssessments->pluck('is_na')->contains(1))
                        <div class="mt-4">
                            <h6 class="font-weight-bold">Principles Not Applicable for this Initiative:</h6>
                            <ul>
                                @foreach ($entry->latest_assessment->principleAssessments->where('is_na', 1) as $principleAssessment)
                                    <li>{{ $principleAssessment->principle->name }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @elseif($entry->latest_assessment->redline_status === \App\Enums\AssessmentStatus::Failed->value)
                    <p class="text-center text-secondary">~~ Red flag assessment failed ~~<br />~~ no principle assessment
                        results available ~~
                    </p>
                @else
                    <p class="text-center text-secondary">~~ Principle assessment not completed ~~<br />~~ no results
                        available ~~
                    </p>
                @endif

            </div>
        </div>

        @if (
            $entry->latest_assessment->principle_status === \App\Enums\AssessmentStatus::Complete->value ||
                $entry->latest_assessment->redline_status === \App\Enums\AssessmentStatus::Failed->value)
            <hr />
            <div class="row mt-4">
                <div class="col-12 d-flex justify-content-center align-items-center">
                    <span class="mr-4 font-lg">Overall Score for this initiative: </span>
                    <span class="font-weight-bold font-xl">{{ $entry->latest_assessment->overall_score }} % </span>
                    <x-help-text-link class="no-print" location="Initiatives - score" type="popover"></x-help-text-link>
                </div>
                <div class="col-12 mt-4 d-flex align-items-center justify-content-center flex-column">
                    @if ($entry->latest_assessment->redline_status === \App\Enums\AssessmentStatus::Failed->value)
                        <span class="text-secondary">Initiatives that fail the red flag assessment automatically receive a
                            score of 0%.</span>
                    @else
                        <span class="text-secondary">Calculated based on the total score, divided by the total possible
                            score for all applicable principles.</span>
                        <span class="font-weight-bold">( {{ $entry->latest_assessment->total }} /
                            {{ $entry->latest_assessment->total_possible }} ) * 100</span>
                    @endif
                </div>
            </div>
        @endif

        @if ($entry->latest_assessment->principle_status === \App\Enums\AssessmentStatus::Complete->value)
            <div class="print-page-break">
                <h1 class="only-print mt-16">{{ $entry->organisation->name }} -
                    {{ \Illuminate\Support\Str::title($entry->name) }}</h1>
                <h4 class="only-print text-uppercase">Principle Assessment Summary</h4>
            </div>

            <div class="row mt-4 pt-4">
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

                        @foreach (\App\Models\Principle::all() as $principle)
                            @php
                                $principleAssessment = $principleAssessments
                                    ->where('principle_id', $principle->id)
                                    ->first();
                            @endphp
                            <tr>
                                <td>{{ $principle->name }}</td>
                                <td> {{ $principleAssessment->is_na ? 'NA' : $principleAssessment->rating }}</td>
                                <td> {{ $principleAssessment->is_na ? '-' : $principleAssessment->rating_comment }}</td>
                                <td>
                                    @if ($principleAssessment->scoreTags()->count() > 0)
                                        <button class="btn btn-link" type="button" data-toggle="modal"
                                            data-target="#modal-shared-{{ $principle->id }}">
                                            {{ $principleAssessment->scoreTags()->count() }} selected
                                        </button>
                                    @else
                                        <span class="btn">0 selected</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($principleAssessment->customScoreTags()->count() > 0)
                                        <button class="btn btn-link" type="button" data-toggle="modal"
                                            data-target="#modal-custom-shared-{{ $principle->id }}">
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

    @foreach (\App\Models\Principle::all() as $principle)
        @php
            $principleProjectModal = $principleAssessments->where('principle_id', $principle->id)->first();
        @endphp


        {{-- Modal for Shared  Examples / Indicators --}}
        <div class="modal fade" tabindex="-1" role="dialog" id="modal-shared-{{ $principle->id }}">
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
                            @foreach ($principleProjectModal->scoreTags as $scoreTag)
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

        {{-- Modal for CUSTOM  Examples / Indicators --}}
        <div class="modal fade" tabindex="-1" role="dialog" id="modal-custom-shared-{{ $principle->id }}">
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
                            @foreach ($principleProjectModal->customScoreTags as $scoreTag)
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
    @endif

    <div class="container no-print">
        @if (count($entry->assessments) > 1)
            <div class="row mt-4 pt-4">
                <h4 class="text-uppercase">Previous Assessments</h4>
                <div class="col-12">
                    <table class="table table-borderless table-responsive">
                        <tr>
                            <th>Assessment</th>
                            <th>Completion Date</th>
                            <th></th>
                        </tr>
                        @foreach ($entry->assessments as $assessment)
                            @if (!$loop->last)
                                <tr>
                                    <td> Assessment {{ $loop->index + 1 }}</td>
                                    <td> {{ $assessment->completed_at }}</td>
                                    <td>
                                        <a class="btn btn-success mr-2"
                                            href="/admin/assessment/{{ $assessment->id }}/show">Show Information</a>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </table>
                </div>
            </div>
        @endif
    </div>

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

        var margin = {
                top: 100,
                right: 100,
                bottom: 100,
                left: 100
            },
            width = document.getElementById('radarChart').offsetWidth - margin.right - margin.left,
            height = Math.min(width, window.innerHeight - margin.top - margin.bottom - 20);

        // set minimum width;
        if (width < 250) {
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
