@extends('email.layout')

@section('content')
    start foreach
    @foreach ($sections as $type => $section)
        @include ('email.partials.' . $type, ['section' => $section])
    @endforeach
    end foreach
@stop
