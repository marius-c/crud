@if($column->input == 'checkbox')
    <div class="inline-container">
        <div class="ui toggle checkbox {{$column->editable ? '' : 'read-only'}} ">
            @include('themes.semantic.inc.form.input')
            <label></label>
        </div>
    </div>
@else
    {!!$value!!}
    <div style="display:none" class="inline-container inline-hidden">
        @include('themes.semantic.inc.form.input')
    </div>
@endif