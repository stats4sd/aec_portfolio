@extends(backpack_view('blank'))

@section('content')

    <div class="container mt-16" id="initiativesListPage">
        <h2 class="font-weight-bold text-bright-green">Initiatives </h2>

        {{-- Actions --}}
        <div class="d-flex justify-content-between mt-8 mb-8">
            <div>
                <button class="btn btn-info mr-4">Add Initiative</button>
                <button class="btn btn-success">Import Initiatives</button>
            </div>
        </div>

        <initiatives-list
            :initiatives="{{ $projects->toJson() }}"
            :has-additional-assessment="{{ $has_additional_assessment ? 'true' : 'false' }}"
        >

    </div>

@endsection
