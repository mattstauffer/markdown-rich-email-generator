@extends('email.layout')

@section('lead')
    @include ('email.partials.lead', ['lead' => $lead])
@stop

@section('content')
    @foreach ($sections as $section)
        <table class="row box type-{{ $section['key'] }}">
            <tr>
                @include ('email.partials.' . $section['key'], ['section' => $section['content']])
            </tr>
        </table>
    @endforeach
@stop
