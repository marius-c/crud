<?php if (!isset($input)) $input = $column->input; ?>
<?php if (!isset($editable)) $editable = $column->editable; ?>




@if($input == 'select')
    <select id="{{$column->name}}" name="{{$column->name}}" {!!$column->inputAttr($crud->form->getValue($column))!!}>
        <option value="">{{$column->input_label}}</option>
        @foreach($crud->form->value($column->options) as $k => $v)
            <option value="{{$k}}" {{$crud->form->getValue($column) == $k ? 'selected' : ''}}>{{$v}}</option>
        @endforeach
    </select>
@endif

@if($input == 'chosen')
    <select class="chosen" id="{{$column->name}}"
            name="{{$column->name}}" {!!$column->inputAttr($crud->form->getValue($column))!!}>
        <option value="">{{$column->input_label}}</option>
        @foreach($crud->form->value($column->options) as $k => $v)
            <option value="{{$k}}" {{$crud->form->getValue($column) == $k ? 'selected' : ''}}>{{$v}}</option>
        @endforeach
    </select>
@endif

@if($input == 'multiselect')
    <select data-placeholder="{{$column->input_label}}" class="multiselect" id="{{$column->name}}"
            name="{{$column->name}}[]" {!!$column->inputAttr($crud->form->getValue($column))!!} multiple>
        @foreach($crud->form->value($column->options) as $k => $v)
            <option value="{{$k}}" {{in_array($k, $crud->form->getValue($column))  ? 'selected' : ''}}>{{$v}}</option>
        @endforeach
    </select>
@endif

@if($input == 'tags')
    <input class="tags" id="{{$column->name}}"
           name="{{$column->name}}" {!!$column->inputAttr($crud->form->getValue($column))!!}/>
@endif

@if($input == 'textarea')
    <textarea {!!$column->inputAttr($crud->form->getValue($column))!!} id="{{$column->name}}"
              name="{{$column->name}}" {{$column->required ? 'required' : ''}}>{{$crud->form->getValue($column)}}</textarea>
@endif

@if($input == 'html')
    <textarea class="html" {!!$column->inputAttr($crud->form->getValue($column))!!} id="{{$column->name}}"
              name="{{$column->name}}" {{$column->required ? 'required' : ''}}>{{$crud->form->getValue($column)}}</textarea>
@endif

@if($input == 'checkbox')
    <input {!!$column->inputAttr()!!} {{$crud->form->getValue($column) ? 'checked ' : ''}}type="checkbox"
           name="{{$column->name}}">
@endif

@if($input == 'text')
    <div class="ui fluid {{$column->input_action ? ' action' : ''}}{{$column->input_icon ? ' left icon' : ''}}{{$column->labeled ? ' labeled' : ''}}{{$column->labeled_right ? ' labeled' : ''}} input">
        @if($column->labeled)
            <div class="ui {{$column->labeled_class}} label">
                {{$column->labeled}}
                {{$column->labeled_right}}
            </div>
        @endif
        <input {!!$column->inputAttr($crud->form->getValue($column))!!} type="text" id="{{$column->name}}"
               name="{{$column->name}}" {{$column->required ? 'required' : ''}}"/>
        @if($column->input_icon)
            <i class="{{$column->input_icon}} icon"></i>
        @endif
        {!!$column->input_action!!}
    </div>
@endif

@if($input == 'datetime')
    <input {!!$column->inputAttr($crud->form->getValue($column))!!} class="datetimepicker" type="text"
           id="{{$column->name}}" name="{{$column->name}}" {{$column->required ? 'required' : ''}}/>
@endif

@if($input == 'interval')
    <div class="ui two column grid">
        <div class="column" style="width:20%;">
            <input interval="{{$column->name}}" placeholder="From"
                   {!!$column->inputAttr($crud->form->getValue($column))!!} type="text" id="{{$column->name}}"
                   name="{{$column->name}}[from]" {{$column->required ? 'required' : ''}}/>
        </div>
        <div class="column" style="width:20%;">
            <input interval="{{$column->name}}" placeholder="To"
                   {!!$column->inputAttr($crud->form->getValue($column))!!} type="text" id="{{$column->name}}"
                   name="{{$column->name}}[to]" {{$column->required ? 'required' : ''}}/>
        </div>
    </div>
@endif

@if($input == 'date')
    <input {!!$column->inputAttr($crud->form->getValue($column))!!} class="datepicker" type="text"
           id="{{$column->name}}" name="{{$column->name}}" {{$column->required ? 'required' : ''}}/>
@endif