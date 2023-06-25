@extends(backpack_view('blank'))

@section('content')

    <div class="container-fluid mt-16" id="initiativesListPage">
        <h1 class="font-weight-bold text-deep-green mb-4">{{ $organisation->name }} - Initiatives </h1>
         <initiatives-list
                :initiatives="{{ $projects->toJson() }}"
                :has-additional-assessment="{{ $has_additional_assessment ? 'true' : 'false' }}"
            />
    </div>

@endsection

@section('after_scripts')
    @vite('resources/js/initiatives.js')
@endsection
