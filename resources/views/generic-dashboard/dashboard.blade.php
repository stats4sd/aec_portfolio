@extends('backpack::layouts.top_left')

@section('content')

    <div class="container mt-16" id="dashboard">
        <h2 class="font-weight-bold text-bright-green">{{ $organisation->name }} - Summary</h2>
        <main-dashboard
            :user="{{ Auth::user() }}"
            :organisation="{{ $organisation->toJson() }}"
        />

    </div>
@endsection

@section('after_scripts')

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
