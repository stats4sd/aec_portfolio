@extends(backpack_view('blank'))

@section('content')

    <div class="container mt-16" id="initiativesListPage">
        <h2 class="font-weight-bold text-bright-green">Initiatives </h2>
         <initiatives-list
                :initiatives="{{ $projects->toJson() }}"
                :has-additional-assessment="{{ $has_additional_assessment ? 'true' : 'false' }}"
            />
    </div>

@endsection

@section('after_scripts')
    @vite('resources/js/initiatives.js')
@endsection
