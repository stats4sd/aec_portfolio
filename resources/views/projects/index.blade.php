@extends(backpack_view('blank'))

@section('content')

    <div class="container-fluid mt-16" id="initiativesListPage">
        <initiatives-list
            :organisation="{{ $organisation }}"
            :initiatives="{{ $projects->toJson() }}"
            :has-additional-assessment="{{ $has_additional_assessment ? 'true' : 'false' }}"
        />
    </div>

@endsection

@section('after_scripts')
    @vite('resources/js/initiatives.js')
@endsection
