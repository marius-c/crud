
@if($column->input == 'checkbox')
    <select id="{{$column->name}}" name="{{$column->name}}" {!!$column->inputAttr($crud->form->getValue($column))!!}>
        <option value="">{{$column->input_label}}</option>
        <option value="1">Checked</option>
        <option value="0">Non-Checked</option>
    </select>
@elseif($column->input == 'datetime')
    <div class="ui two column grid">
        <div class="column">
            <input placeholder="From {{$column->title}}" name="interval-from-{{$column->name}}" type="text" class="datetimepicker"/>
        </div>
        <div class="column">
            <input placeholder="To {{$column->title}}" name="interval-to-{{$column->name}}" type="text" class="datetimepicker"/>
        </div>
    </div>
@else
    @include('themes.semantic.inc.form.input')
@endif
