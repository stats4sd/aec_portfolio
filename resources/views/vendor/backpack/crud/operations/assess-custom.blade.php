@extends(backpack_view('blank'))

@section('content')

    <div class="container">

        <div class="d-flex justify-content-between py-3">
            <a href="{{ backpack_url('project') }}"><i class="las la-backward"></i> Back to all initiatives</a>
        </div>


        <div class="row" id="aePrinciplesAssessment">
            <v-app>
                <Suspense>
                    <agroecological-principles-assessment :assessment="{{ $assessment->toJson() }}" assessment-type="additional"/>
                </Suspense>
            </v-app>
        </div>
    </div>
@endsection

@section('after_scripts')
    @vite('resources/js/assess.js')
@endsection
