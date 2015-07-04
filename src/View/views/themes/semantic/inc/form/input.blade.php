
<?php if(!isset($input)) $input = $column->input; ?>
<?php if(!isset($editable)) $editable = $column->editable; ?>


@if($input == 'custom_html')
    {!!$column->input_custom_html!!}
@endif

@if($input == 'select')
    <select multiple id="{{$column->name}}" name="{{$column->name}}" {!!$column->inputAttr($crud->form->getValue($column))!!}>
        <option value="">{{$column->input_label}}</option>
        @foreach($crud->form->value($column->options) as $k => $v)
            <option value="{{$k}}" {{$crud->form->getValue($column) == $k ? 'selected' : ''}}>{{$v}}</option>
        @endforeach
    </select>
@endif

@if($input == 'chosen')
    <select data-placeholder="{{$column->input_label}}" class="chosen" id="{{$column->name}}" name="{{$column->name}}" {!!$column->inputAttr($crud->form->getValue($column))!!}>
        @foreach($crud->form->value($column->options) as $k => $v)
            <option value="{{$k}}" {{$crud->form->getValue($column) == $k ? 'selected' : ''}}>{{$v}}</option>
        @endforeach
    </select>
@endif

@if($input == 'chosen-multiple' || $input == 'multiselect')
    <select multiple data-placeholder="{{$column->input_label}}" class="chosen" id="{{$column->name}}" name="{{$column->name}}[]" {!!$column->inputAttr($crud->form->getValue($column))!!}>
        @foreach($crud->form->value($column->options) as $k => $v)
            <option value="{{$k}}" {{in_array($k, $crud->form->getValue($column))  ? 'selected' : ''}}>{{$v}}</option>
        @endforeach
    </select>
@endif

@if($input == 'file')
    <div id="filesContainer{{$column->name}}"></div>

    <script type="text/jsx">
        React.render(
            <FilesSlots files="{{$crud->form->row ? $crud->form->row->filesByColumn($column->name)->toJson() : '[]'}}" empty_slots="{{$column->file_empty_slots}}" name="{{$column->name}}" target="/files/upload?_token={{csrf_token()}}"/>,
            document.getElementById('filesContainer{{$column->name}}')
        );
    </script>
@endif

@if($input == 'tags')
    <input class="tags" id="{{$column->name}}" name="{{$column->name}}" {!!$column->inputAttr($crud->form->getValue($column))!!}/>
@endif

@if($input == 'textarea')
    <textarea {!!$column->inputAttr($crud->form->getValue($column))!!} id="{{$column->name}}" name="{{$column->name}}" {{$column->required ? 'required' : ''}}>{{$crud->form->getValue($column)}}</textarea>
@endif

@if($input == 'html')
    <textarea class="html" {!!$column->inputAttr($crud->form->getValue($column))!!} id="{{$column->name}}" name="{{$column->name}}" {{$column->required ? 'required' : ''}}>{{$crud->form->getValue($column)}}</textarea>
@endif

@if($input == 'checkbox')
    <input {!!$column->inputAttr()!!} {{$crud->form->getValue($column) ? 'checked ' : ''}}type="checkbox" name="{{$column->name}}">
@endif

@if($input == 'text')
    <div class="ui fluid{{$column->input_action ? ' action' : ''}}{{$column->input_icon ? ' left icon' : ''}}{{$column->input_right_icon ? ' right icon' : ''}}{{$column->labeled ? ' labeled' : ''}}{{$column->labeled_right ? ' right labeled' : ''}} input">
        @if($column->input_icon)
            <i class="{{$column->input_icon}} icon"></i>
        @endif
        @if($column->labeled)
            <div class="ui {{$column->labeled_class}} label">
                {{$column->labeled}}
            </div>
        @endif
        <input placeholder="{{$column->placeholder}}" {!!$column->inputAttr($crud->form->getValue($column))!!} type="text" id="{{$column->name}}" name="{{$column->name}}" {{$column->required ? 'required' : ''}}"/>
        @if($column->labeled_right)
            <div class="ui {{$column->labeled_class}} label">
                {{$column->labeled_right}}
            </div>
        @endif
        @if($column->input_right_icon)
            <i class="{{$column->input_right_icon}} icon"></i>
        @endif
        {!!$column->input_action!!}
    </div>
@endif

@if($input == 'datetime')
    <input {!!$column->inputAttr($crud->form->getValue($column))!!} class="datetimepicker" type="text" id="{{$column->name}}" name="{{$column->name}}" {{$column->required ? 'required' : ''}}/>
@endif

@if($input == 'interval')
    <div class="ui two column grid">
        <div class="column" style="width:20%;">
            <input interval="{{$column->name}}" placeholder="From" {!!$column->inputAttr($crud->form->getValue($column))!!} type= "text" id="{{$column->name}}" name="{{$column->name}}[from]" {{$column->required ? 'required' : ''}}/>
        </div>
        <div class="column" style="width:20%;">
            <input interval="{{$column->name}}" placeholder="To" {!!$column->inputAttr($crud->form->getValue($column))!!} type="text" id="{{$column->name}}" name="{{$column->name}}[to]" {{$column->required ? 'required' : ''}}/>
        </div>
    </div>
@endif

@if($input == 'date')
    <input {!!$column->inputAttr($crud->form->getValue($column))!!} class="datepicker" type="text" id="{{$column->name}}" name="{{$column->name}}" {{$column->required ? 'required' : ''}}/>
@endif