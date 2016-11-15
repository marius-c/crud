@extends('themes.semantic.layout')

@section('content')
    <div class="ui blue menu">
        <a class="{{$crud->filters->active()->count() ? '' : 'active '}}item" href="{{$crud->url()}}">
            <i class="table icon"></i>
            {{$crud->getTitle()}}
        </a>

        @foreach($crud->filters->buttonable() as $filter)
            <a class="item{{$filter->isActive() ? ' active' : ''}}" href="{{$crud->router['table']->url($crud->filters->toggle($filter))}}">
                @if($filter->isActive())
                    <i class="remove icon"></i>
                @else
                    <i class="filter icon"></i>
                @endif
                {{$filter->getLabel()}}
            </a>

        @endforeach


        <div class="right menu">
            <div class="item">
                <div class="ui icon input">
                    <input type="text" placeholder="Search..." onkeyup="$('#crud{{$crud->id}}_filter input').val(this.value).keyup()">
                    <i class="search icon"></i>
                </div>
            </div>

            @if($crud->options['advanced_search_enabled'])
                <a class="item" advanced-search-launcher>
                    <i class="filter icon"></i>
                    Advanced search
                </a>
            @endif

            @if($crud->options['global_actions_enabled'])
                @foreach($crud->actions->tag('global') as $action)
                    {!!$action->html(null, ['class'=> 'ui blue item'])!!}
                @endforeach
            @endif
        </div>
    </div>

    @include('themes.semantic.inc.table.advanced-search-form')

    {!!$crud->tableStyle->getBefore()!!}
    <table id="crud{{$crud->id}}" class="ui crud table unstackable" style="width:100%!important">
        <thead>
            <tr>
                @foreach($crud->columns->tableable() as $i => $column)
                    <th data-index="{{$i}}" class="{{$column->expandable ? 'expandable-container' : ''}}">
                        {{$column->title}}
                    </th>
                @endforeach

                @if($crud->shouldDisplayRowsActions())
                    <th>Actions</th>
                @endif
            </tr>
        </thead>
        @if($crud->options['search_filters_enabled'])
            <thead>
                <tr class="search-filters">
                    @foreach($crud->columns->tableable() as $column)

                        <td style="padding-right:25px;">
                            @if($column->database && ! $column->expandable)
                                @if($column->input == 'checkbox')
                                    <?php $column->default = 0; ?>
                                    <div class="ui checkbox toggle">
                                        @include('themes.semantic.inc.form.input', ['input' => $column->search_input, 'editable' => true])
                                    </div>
                                @else
                                    <div class="ui form">
                                        @include('themes.semantic.inc.form.input', ['input' => $column->search_input])
                                    </div>
                                @endif
                            @else

                            @endif
                        </td>
                    @endforeach
                </tr>
            </thead>
        @endif
        <tbody>

        </tbody>
    </table>
@stop

@section('js')
    @include('themes.semantic.inc.table.scripts.pagination')

    <script>
        var crudSelector = '{{$crud->selector}}',
            afterDatatablesCallbacks = [];
    </script>

    @include('global.inc.table.scripts.ajax-setup')
    @include('global.inc.table.scripts.confirm-action')
    @include('global.inc.table.scripts.ajax-actions')
    @include('global.inc.table.scripts.initialize-datatables')
    @include('global.inc.table.scripts.search-filters')
    @include('global.inc.table.scripts.inline-edit')
    @include('global.inc.table.scripts.rows-checkbox')
    @include('global.inc.table.scripts.expandable')
    @yield('js')

    {!!$crud->tableStyle->getAfter()!!}

@overwrite
