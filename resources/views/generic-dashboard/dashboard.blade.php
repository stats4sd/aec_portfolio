@extends('backpack::layouts.top_left')

@section('content')

    <div class="mt-16 container-fluid" id="dashboard">

        <div class="d-flex justify-content-between">
            <div class="d-flex align-items-center mb-4">
            <h1 class="font-weight-bold text-deep-green mb-0">{{ $organisation->name }} - Summary</h1>
                <x-help-text-link class="font-3xl" location="Dashboard - page title"/>
            </div>

            @if(Auth::user()->can('download project-level data'))
            <div>
                <a class="btn btn-info" href="{{ url('/admin/organisation/export') }}">Export All Initiative Data</a>
            </div>
            @endif
        </div>

        <x-help-text-entry class="mb-4" location="Dashboard - page title"/>

        <v-app>
            <Suspense>
                <main-dashboard
                    :user="{{ Auth::user() }}"
                    :organisation="{{ $organisation->toJson() }}"
                    :regions="{{ $regions }}"
                    :countries="{{ $countries }}"
                    :categories="{{ $categories }}"
                />
            </Suspense>
        </v-app>

    </div>
@endsection

@section('after_scripts')
    @vite('resources/js/dashboard.js')
@endsection
