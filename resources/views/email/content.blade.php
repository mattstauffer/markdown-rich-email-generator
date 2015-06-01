@extends('email.layout')

@section('lead')
    @include ('email.partials.lead', ['lead' => $lead])
@stop

@section('content')
    <?php $i = 0; ?>
    @foreach ($sections as $section)
        <table class="row box {{ $i == 0 ? 'box-first' : '' }} type-{{ $section['key'] }}">
            <tr>
                @include ('email.partials.' . $section['key'], ['section' => $section['content']])
            </tr>
        </table>
        <?php $i++; ?>
    @endforeach
@stop
