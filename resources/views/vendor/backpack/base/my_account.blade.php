@extends(backpack_view('blank'))

@section('after_styles')
    <style media="screen">
        .backpack-profile-form .required::after {
            content: ' *';
            color: red;
        }
    </style>
@endsection

@php
    $breadcrumbs = [
        trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
        trans('backpack::base.my_account') => false,
    ];
@endphp

@section('header')
    <section class="content-header">
        <div class="container-fluid mb-3">
            <h1>{{ trans('backpack::base.my_account') }}</h1>
            <h4>ROLE: {{ auth()->user()->getRoleNames()->join(', ') }}</h4>
            @if(\App\Models\Organisation::count() < 2)
                <h4>INSTITUTION: {{ \App\Models\Organisation::first()->name }}</h4>
            @endif
        </div>
    </section>
@endsection

@section('content')
    <div class="row">

        @if (session('success'))
            <div class="col-lg-8">
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if ($errors->count())
            <div class="col-lg-8">
                <div class="alert alert-danger">
                    <ul class="mb-1">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="col-lg-8">

            @if(\App\Models\Organisation::count() > 1)
                <div class="card padding-10">
                    <div class="card-header">
                        <h5>My Institution(s)</h5>
                        <p>Below are all the institutions that you have access to. The highlighted one is currently selected, and every other page on the site will present information only for the chosen institution. To use the tool on behalf of a different institution, click on one of the other items in the list below.</p>
                    </div>
                    <div class="card-body backpack-profile-form bold-labels">
                        <div class="list-group list-group-flush">

                            @foreach(\App\Models\Organisation::all() as $organisation)
                                <button class="row list-group-item @if($organisation->id === (int) Session::get('selectedOrganisationId')) list-group-item-success @endif" type="button" onclick="submitForm(this)" id="{{$organisation->id}}">
                                    <div class="col-12">
                                        {{ $organisation->name }}
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <form class="form" action="{{ route('backpack.account.info.store') }}" method="post">

                {!! csrf_field() !!}

                <div class="card padding-10">

                    <div class="card-header">
                        {{ trans('backpack::base.update_account_info') }}
                    </div>

                    <div class="card-body backpack-profile-form bold-labels">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                @php
                                    $label = trans('backpack::base.name');
                                    $field = 'name';
                                @endphp
                                <label class="required">{{ $label }}</label>
                                <input required class="form-control" type="text" name="{{ $field }}" value="{{ old($field) ? old($field) : $user->$field }}">
                            </div>

                            <div class="col-md-6 form-group">
                                @php
                                    $label = config('backpack.base.authentication_column_name');
                                    $field = backpack_authentication_column();
                                @endphp
                                <label class="required">{{ $label }}</label>
                                <input required class="form-control" type="{{ backpack_authentication_column()==backpack_email_column()?'email':'text' }}" name="{{ $field }}" value="{{ old($field) ? old($field) : $user->$field }}">
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">
                            <i class="la la-save"></i> {{ trans('backpack::base.save') }}
                        </button>
                        <a href="{{ backpack_url() }}" class="btn">{{ trans('backpack::base.cancel') }}</a>
                    </div>
                </div>

            </form>
        </div>

        {{-- CHANGE PASSWORD FORM --}}
        <div class="col-lg-8">
            <form class="form" action="{{ route('backpack.account.password') }}" method="post">

                {!! csrf_field() !!}

                <div class="card padding-10">

                    <div class="card-header">
                        {{ trans('backpack::base.change_password') }}
                    </div>

                    <div class="card-body backpack-profile-form bold-labels">
                        <div class="row">
                            <div class="col-md-4 form-group">
                                @php
                                    $label = trans('backpack::base.old_password');
                                    $field = 'old_password';
                                @endphp
                                <label class="required">{{ $label }}</label>
                                <input autocomplete="new-password" required class="form-control" type="password" name="{{ $field }}" id="{{ $field }}" value="">
                            </div>

                            <div class="col-md-4 form-group">
                                @php
                                    $label = trans('backpack::base.new_password');
                                    $field = 'new_password';
                                @endphp
                                <label class="required">{{ $label }}</label>
                                <input autocomplete="new-password" required class="form-control" type="password" name="{{ $field }}" id="{{ $field }}" value="">
                            </div>

                            <div class="col-md-4 form-group">
                                @php
                                    $label = trans('backpack::base.confirm_password');
                                    $field = 'confirm_password';
                                @endphp
                                <label class="required">{{ $label }}</label>
                                <input autocomplete="new-password" required class="form-control" type="password" name="{{ $field }}" id="{{ $field }}" value="">
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">
                            <i class="la la-save"></i> {{ trans('backpack::base.change_password') }}
                        </button>
                        <a href="{{ backpack_url() }}" class="btn">{{ trans('backpack::base.cancel') }}</a>
                    </div>

                </div>

            </form>
        </div>

        <div class="col-lg-8">
            <div class="card padding-10">

                <div class="card-header d-flex align-items-flex-end justify-content-between">
                    <div>
                        <b><a href="./my-role/request-to-leave">Request to leave an institution</a></b>
                    </div>
                </div>
            </div>
        </div>

        @if ( auth()->user()?->hasRole('Institutional Admin') )
            <div class="col-lg-8">
                <div class="card padding-10">
                    <div class="card-header d-flex align-items-flex-end justify-content-between">
                        <div>
                            <b><a href="./my-role/request-to-remove-everything">Request to remove everything for institution</a></b>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <form name="selectedOrganisationForm" action="{{ backpack_url('selected_organisation') }}" method="POST">
            @csrf
            @method('POST')
            <input type="hidden" name="organisationId">
            <input type="hidden" name="redirect" value="{{ backpack_url('edit-account-info') }}">
        </form>

    </div>
@endsection

@section('after_scripts')
    @vite('resources/js/app.js')

    <script>

        function submitForm(organisationButton) {
            this.document.selectedOrganisationForm.organisationId.value = organisationButton.id;
            this.document.selectedOrganisationForm.submit();
        }

    </script>
