<div class=" pt-16 d-flex justify-content-between w-100">

    <h1 class="text-deep-green"><b>{{$organisation->name}}</b></h1>
    @can('update', $organisation)
        <div>
            <a href="{{ route('organisation.edit', $organisation->id) }}" class="btn btn-primary">Edit
                Institution</a>
        </div>
    @endcan
</div>
