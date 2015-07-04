<div class="field {{$column->required ? 'required' : ''}}" {!!$column->fieldAttr()!!}>
    <label for="{{$column->name}}">{{$column->input_label}}:</label>
    @include('themes.semantic.inc.form.search-input')
    @include('global.inc.form.helpers.max-length')

    @if($column->help_block)
        <div class="help-block">{{$column->help_block}}</div>
    @endif
</div>
