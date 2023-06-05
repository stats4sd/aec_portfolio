@extends('backpack::layouts.top_left')

@section('content')

<div class="container">

<div class="row pl-4 pt-4 w-100">
    <div class="col-12 col-xl-12 card">
        <h2>You have selected {{ $organisation->name }}</h2>
    </div>
</div>

@endsection
