<div class="card-header">
    <h2>Institution Portfolios</h2>
    <p class="help-block">Review and manage the list of portfolios. All initiatives entered into the platform are part of a portfolio. <br/> <br/> If your institution just has 1 portfolio, please add it here.</p>
</div>

<div class="card-body">

    <table class="table table-striped">
        <thead>
        <tr>
            <th scope="col">Portfolio Name</th>
            <th scope="col"># of Initiatives</th>
            <th scope="col"># of Fully Assessed Initiatives</th>
            <th scope="col">Actions</th>
        </tr>
        </thead>
        <tbody>
        @if(Auth::user()->can('view portfolios'))
            @foreach($organisation->portfolios as $portfolio)
                <tr>
                    <td>
                        {{ $portfolio->name }}
                    </td>
                    <td>{{ $portfolio->projects->count() }}</td>
                    <td>{{ $portfolio->projects->filter(fn(\App\Models\Project $initiative): bool => $initiative->latest_assessment->completed_at !== null)->count() }}</td>
                    <td>
                        <div class="btn-group">

                            <a href="{{ url("admin/project?portfolio=$portfolio->name") }}" class="btn btn-success btn-sm">SHOW INITIATIVES</a>
                            @if(Auth::user()->can('maintain portfolios'))
                                <a href="{{ route('portfolio.edit', [$portfolio]) }}" class="btn btn-info btn-sm">EDIT</a>
                                <button class="btn btn-danger btn-sm remove-button" data-portfolio="{{ $portfolio->id }}" data-toggle="modal" data-target="#removePortfolioModal{{ $portfolio->id }}">REMOVE</button>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>
    <hr/>

    @if(Auth::user()->can('maintain portfolios'))
        <a class="btn btn-dark mt-5" href="{{ route('portfolio.create') }}">Add Portfolio</a>
    @endif
</div>


@push('after_scripts')
    @foreach($organisation->portfolios as $portfolio)
        <div class="modal fade" id="removePortfolioModal{{ $portfolio->id }}" tabindex="-1" role="dialog" aria-labelledby="removeUserModalLabel{{ $portfolio->id }}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="removeUserModalLabel{{ $portfolio->id }}">Remove {{ $portfolio->email }} from {{ $organisation->name }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Are you sure you wish to remove {{ $portfolio->name }} from {{ $organisation->name }}?<br/><br/>
                        <span class="font-weight-bold text-danger">This will delete all initiatives that are part of this portfolio!</span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <form action="{{ route('portfolio.destroy', [$portfolio]) }}" method="POST">
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