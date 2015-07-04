@extends('global.layout')

@section('head')
    @include('themes.bootstrap.inc.style.css')
    @include('themes.bootstrap.inc.assets')
@stop

@section('body')
    <div class="ui view basic segment" style="padding:0!important;">
        <div class="crud-container" ng-app="crudApp">
            @yield('content')
        </div>
    </div>

    <script>
        @include('global.inc.form.scripts.initialize-fields')
    </script>
    @yield('js')
@stop