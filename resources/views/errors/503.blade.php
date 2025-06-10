@extends('errors.layout')

@php
  $error_number = 503;
@endphp

@section('title')
  It's not you, it's me.
@endsection

@section('description')
  @php
    $default_error_message = "This site is temporarily offline while we deploy updates and run some maintenance checks. Please try again in a few minutes.";
  @endphp
  {!! isset($exception)? ($exception->getMessage()?$exception->getMessage():$default_error_message): $default_error_message !!}
@endsection
