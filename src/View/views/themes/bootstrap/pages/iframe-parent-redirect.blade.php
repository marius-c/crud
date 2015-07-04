@extends('themes.bootstrap.layout')

@section('content')
    @include('themes.bootstrap.inc.loader', ['text' => 'Redirecting...'])
@stop

@section('js')
    <script>
        parent.location = '{{$url}}';
    </script>
@stop