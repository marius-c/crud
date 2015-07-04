@extends('themes.semantic.layout')

@section('content')
    @include('themes.semantic.inc.loader', ['text' => 'Redirecting...'])
@stop

@section('js')
    <script>
        parent.location = '{{$url}}';
    </script>
@stop