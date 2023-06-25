<div class="card-header">
    <h2>Additional Assessment Criteria</h2>
    <p class="help-block">Your Institution uses an additional set of assessment criteria beyond the 13 Agroecology Principles. These assessment criteria are used in an extra assessment section after the main Agroecology assessment. They are not shown in the main dashboards, but the information entered and the results are included in any datasets downloaded from the platform by your institution.</p>
</div>

<div class="card-body">

    <table class="table table-striped">
        <thead>
        <tr>
            <th scope="col">Name</th>
            <th scope="col">Link to information</th>
            <th scope="col">Can be "NA"?</th>
            <th scope="col">Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($organisation->additionalCriteria as $additionalCriteria)
            <tr>
                <td>
                    {{ $additionalCriteria->name }}
                </td>
                <td>{{ $additionalCriteria->link }}</td>
                <td>{{ $additionalCriteria->can_be_na ? "YES" : "NO" }}</td>
                <td>
                    <div class="btn-group">

                        <a href="{{ route('additional-criteria.show', $additionalCriteria) }}" class="btn btn-success btn-sm">REVIEW</a>
                        @if(Auth::user()->can('maintain portfolios'))
                            <a href="{{ route('additional-criteria.edit', [$organisation, $additionalCriteria]) }}" class="btn btn-info btn-sm">EDIT</a>
                            <button class="btn btn-danger btn-sm remove-button" data-portfolio="{{ $additionalCriteria->id }}" data-toggle="modal" data-target="#removePortfolioModal{{ $additionalCriteria->id }}">REMOVE</button>
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <hr/>

    @if(Auth::user()->can('maintain portfolios'))
        <a class="btn btn-dark mt-5" href="{{ route('additional-criteria.create') }}">Add Additional Criterium</a>
    @endif
</div>


@push('after_scripts')
    @foreach($organisation->additionalCriteria as $additionalCriteria)
        <div class="modal fade" id="removePortfolioModal{{ $additionalCriteria->id }}" tabindex="-1" role="dialog" aria-labelledby="removeUserModalLabel{{ $additionalCriteria->id }}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="removeUserModalLabel{{ $additionalCriteria->id }}">Remove {{ $additionalCriteria->email }} from {{ $organisation->name }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Are you sure you wish to remove {{ $additionalCriteria->name }} from {{ $organisation->name }}?<br/><br/>
                        <span class="font-weight-bold text-danger">This will delete all initiatives that are part of this portfolio!</span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <form action="{{ route('portfolio.destroy', [$additionalCriteria]) }}" method="POST">
                            @csrf
                            @method('delete')
                            <button type="submit" class="btn btn-primary">Confirm Remove</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endpush
