@extends('backpack::layouts.top_left')

@section('content')

    <a href="{{backpack_url('project')}}" class="btn btn-link">Back to projects list</a>

    <div class="container mt-4">

        <h1>Project Review Page</h1>
        <h2>{{ $entry->organisation->name }} - {{ $entry->name }}</h2>

        <div class="row mt-3">
            <div class="col-12 col-md-6 col-lg-6">

                <table class="table table-borderless">
                    <tr>
                        <td class="text-right pr-4 mr-2">Project Code:</td>
                        <td>{{ $entry->code }}</td>
                    </tr>
                    <tr>
                        <td class="text-right pr-4 mr-2">Budget:</td>
                        <td>USD {{ $entry->budget }}</td>
                    </tr>
                    <tr>
                        <td class="text-right pr-4 mr-2">Status:</td>
                        <td>{{ $entry->assessment_status }}</td>
                    </tr>
                    <tr>
                        <td class="text-right pr-4 mr-2">Ratings Total:</td>
                        @if($entry->assessment_status === \App\Enums\AssessmentStatus::Complete)
                            <td>{{ $entry->total }} / {{ $entry->totalPossible }}</td>
                        @else
                            <td class="text-secondary">~~ Assessment not yet completed ~~</td>
                        @endif
                    </tr>
                    <tr>
                        <td class="text-right pr-4 mr-2">Overall Score:</td>
                        @if($entry->assessment_status === \App\Enums\AssessmentStatus::Complete)
                            <td><span class="font-weight-bold">{{ $entry->overall_score }} % </span>
                                <br/>
                                <span class="text-sm text-secondary">Calculated based on {{ $entry->principleProjects()->where('is_na', 0)->count() }} / 13 relevant principles.</span>
                            </td>
                        @else
                            <td class="text-secondary">~~ Assessment not yet completed ~~</td>
                        @endif
                    </tr>
                </table>
            </div>

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
                        $principleProjects = $entry->principleProjects;
                    @endphp

                    @foreach(\App\Models\Principle::all() as $principle)

                        @php
                            $principleProject = $principleProjects->where('principle_id', $principle->id)->first();
                        @endphp
                        <tr>
                            <td>{{ $principle->name }}</td>
                            <td> {{ $principleProject->is_na ? "NA" : $principleProject->rating }}</td>
                            <td> {{ $principleProject->is_na ? "-" : $principleProject->rating_comment }}</td>
                            <td>
                                @if($principleProject->scoreTags()->count() > 0)
                                    <button class="btn btn-link" type="button" data-toggle="modal"
                                            data-target="#modal-shared-{{$principle->id}}">
                                        {{ $principleProject->scoreTags()->count() }} selected
                                    </button>
                                @else
                                    <span class="btn">0 selected</span>
                                @endif
                            </td>
                            <td>
                                @if($principleProject->customScoreTags()->count() > 0)
                                    <button class="btn btn-link" type="button" data-toggle="modal"
                                            data-target="#modal-custom-shared-{{$principle->id}}">
                                        {{ $principleProject->customScoreTags()->count() }} added
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
            $principleProjectModal = $principleProjects->where('principle_id', $principle->id)->first();
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
    <script src="{{ mix('js/app.js') }}"></script>
@endsection
