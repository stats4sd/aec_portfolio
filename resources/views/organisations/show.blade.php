@extends('backpack::layouts.top_left')

@section('content')

<div class="container">

    @include('organisations.header')
    @include('organisations.members')

</div>
@endsection

@section('after_scripts')
<script src="{{ mix('js/app.js') }}"></script>
@endsection
