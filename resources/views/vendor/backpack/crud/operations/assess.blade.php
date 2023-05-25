@extends(backpack_view('blank'))

@section('content')

    <div class="container">

        <div class="d-flex justify-content-between py-3">
            <div><i class="las la-backward"></i> Back to all initiatives</div>
        </div>


        <div class="row" id="aePrinciplesAssessment">
            <AgroecologicalPrinciplesAssessment/>
        </div>
    </div>
@endsection
