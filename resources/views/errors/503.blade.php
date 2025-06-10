@extends('errors.layout')

@php
  $error_number = 503;
@endphp

@section('title')
 Updates in progress
@endsection

@section('description')
  @php
    $default_error_message = "This site is temporarily offline while we deploy updates and run some maintenance checks. Please try again in a few minutes.";
  @endphp
  {!! $default_error_message !!}
@endsection
