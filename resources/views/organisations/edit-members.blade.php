@extends('backpack::layouts.top_left')
@section('header')
    <link href="{{ asset('packages/select2/dist/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('packages/select2-bootstrap-theme/dist/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

@include('organisations.header')

<div class="card">
    <div class="card-header">
        Edit Access to the institution: {{ $organisation->name }}
    </div>
    <div class="card-body">
    <form method="POST" action="{{ route('organisationmembers.update', [$organisation, $user])}}">
            @csrf
            @method('put')
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="form-group row">
                <label class="col-md-6 col-form-label text-md-right">
                    User Name
                </label>
                <p class="col-md-6 col-form-label">{{ $user->name }}</p>
            </div>
            <div class="form-group row">
                <label class="col-md-6 col-form-label text-md-right">
                    User Email
                </label>
                <p class="col-md-6 col-form-label">{{ $user->email }}</p>
            </div>
            <div class="form-group row required">
                <label for="select-users" class="col-md-6 col-form-label text-md-right">
                    Assign access level for institution {{ $organisation->name }}
                </label>
                <div class="col-md-6">
                    <select
                        id="access-level"
                        name="role"
                        class="select2 form-control @error('name') is-invalid @enderror"
                        value="{{ $user->pivot->is_admin }}"
                        >
                            <option value="admin" {{ !$user->pivot->admin ? 'selected' : '' }}>Team Administrator</option>
                            <option value="editor" {{ $user->pivot->editor ? 'selected' : '' }}>Editor</option>
                            <option value="viewer" {{ $user->pivot->viewer ? 'selected' : '' }}>Viewer</option>

                    </select>
                    @error('users')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="form-group row mb-0">
                <div class="col-md-10 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        Submit
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
