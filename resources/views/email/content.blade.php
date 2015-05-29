@extends('email.layout')

@section('lead')
    @include ('email.partials.lead', ['lead' => $lead])
@stop

@section('content')
    <?php $i = 0; ?>
    @foreach ($sections as $type => $section)
        <table class="row box {{ $i == 0 ? 'box-first' : '' }} type-{{ $type }}">
            <tr>
                @include ('email.partials.' . $type, ['section' => $section])
            </tr>
        </table>
        <?php $i++; ?>
    @endforeach
@stop
