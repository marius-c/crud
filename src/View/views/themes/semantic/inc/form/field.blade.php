@if($column->input == 'checkbox')
    <div style="height:7px;"></div>
    <div class="ui toggle checkbox" {!!$column->fieldAttr()!!}>
        @include('themes.semantic.inc.form.input')
        <label>{{$column->input_label}}</label>
    </div>
    <div class="clearfix"></div>
    <div style="height:7px;"></div>
@else
    <div class="field {{$column->required ? 'required' : ''}}" {!!$column->fieldAttr()!!}>
        <label for="{{$column->name}}">{{$column->input_label}}:</label>
        @include('themes.semantic.inc.form.input')
        @include('global.inc.form.helpers.max-length')

        @if($column->help_block)
            <div class="help-block">{{$column->help_block}}</div>
        @endif
    </div>
@endif