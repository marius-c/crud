@extends('global.layout')

@section('head')
    @include('themes.semantic.inc.style.css')
    @include('themes.semantic.inc.assets')
@stop

@section('body')
    <div class="ui view basic segment" style="padding:0!important;">
        <div class="ui dimmer">
            <div class="ui large text loader">Processing...</div>
        </div>

        <div class="crud-container" ng-app="crudApp">
            @yield('content')
            {!!$content or ''!!}
        </div>
    </div>

    <script>
        @include('global.inc.form.scripts.initialize-fields')
    </script>
    @yield('js')
@stop