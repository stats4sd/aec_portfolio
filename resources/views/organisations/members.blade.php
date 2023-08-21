<div class="card-header">
    <div class="d-flex align-items-center">
        <h2 class="mb-0">Institution Members</h2>
        <x-help-text-link class="font-2xl" location="My Institution - Institution members"/>
    </div>
    <p class="help-block">Review and manage the list of members with access to this institution's information.</p>

    <x-help-text-entry location="My Institution - Institution members"/>

</div>


<div class="card-body">

    <table class="table table-striped">
        <thead>
        <tr>
            <th scope="col">Name</th>
            <th scope="col">Email</th>
            <th scope="col">Role</th>
            @if(Auth::user()->can('maintain institutional members'))
                <th scope="col">Actions</th>
            @endif
        </tr>
        </thead>
        <tbody>
        @foreach($organisation->users as $user)
            <tr>
                <td>
                    {{ $user->name }}
                </td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->roles->pluck('name')->join(', ') }}</td>
                @if(Auth::user()->can('maintain institutional members'))
                    <td>
                        <a href="{{ route('organisationmembers.edit', [$organisation, $user]) }}" class="btn btn-dark btn-sm" name="edit_member{{ $user->id }}" onclick="">EDIT</a>
                        <button class="btn btn-dark btn-sm remove-button" data-user="{{ $user->id }}" data-toggle="modal" data-target="#removeUserModal{{ $user->id }}">REMOVE</button>
                    </td>
                @endif
            </tr>
        @endforeach
        </tbody>
    </table>
    <hr/>

    @if($organisation->invites->count() > 1)
        <h4>Pending Invites</h4>
    @endif
    <ul class="list-group">
        @foreach($organisation->invites as $invite)
            <li class="list-group-item list-group-flush d-flex">
                <div class="w-50">{{ $invite->email }}</div>
                <div class="w-25">Invited on {{ $invite->invite_day }}</div>
            </li>
        @endforeach
    </ul>

    @if(Auth::user()->can('invite institutional members'))
        <a class="btn btn-dark mt-5" href="{{ route('organisationmembers.create', $organisation) }}">INVITE MEMBERS</a>
    @endif
</div>


@push('after_scripts')
    @foreach($organisation->users as $user)
        <div class="modal fade" id="removeUserModal{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="removeUserModalLabel{{ $user->id }}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="removeUserModalLabel{{ $user->id }}">Remove {{ $user->email }} from {{ $organisation->name }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Are you sure you wish to remove {{ $user->name }} from {{ $organisation->name }}?<br/><br/>After removing, {{ $user->name }} will no longer have access to any data on this data platform.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <form action="{{ route('organisationmembers.destroy', [$organisation, $user]) }}" method="POST">
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
