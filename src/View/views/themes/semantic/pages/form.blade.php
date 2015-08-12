@extends('themes.semantic.layout')
@section('content')

    <div class="ui dividing header" style="font-size:20px;">
        @if($row->id)
            <?php $title = 'Editing'; ?>
        @else
            <?php $title = 'Creating'; ?>
        @endif
        {{$title}}
    </div>

    <form enctype="multipart/form-data" method="post" class="ui form crud-form" action="{{$crud->actions['save']->url($row->id)}}">

        {!!$crud->form->style->getBefore('fields')!!}
        @foreach($crud->columns->whereProp('form') as $column)
            {!!$crud->form->style->getBefore($column->name)!!}

            @include('themes.semantic.inc.form.field')

            {!!$crud->form->style->getAfter($column->name)!!}
        @endforeach
        {!!$crud->form->style->getAfter('fields')!!}

        <div style="text-align:right; margin-top: 20px;">
            @foreach($crud->actions->tag('form') as $action)
                {!!$action->html($row)!!}
            @endforeach
        </div>
        <input name="_token" type="hidden" value="{{csrf_token()}}" />
    </form>
@stop


@section('js')
    @include('themes.semantic.inc.form.scripts.validator')
    @include('global.inc.form.scripts.html-editor')
    @include('global.inc.form.scripts.file-upload')
    @include('global.inc.form.scripts.alert-on-quiting')
@stop