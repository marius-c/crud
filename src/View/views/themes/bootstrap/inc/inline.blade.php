@if($column->input == 'checkbox')
    <div class="inline-container">
        <div class="ui toggle checkbox {{$column->editable ? '' : 'read-only'}} ">
            @include('themes.bootstrap.inc.form.input')
            <label></label>
        </div>
    </div>
@else
    {!!$value!!}
    <div style="display:none" class="inline-container inline-hidden">
        @include('themes.bootstrap.inc.form.input')
    </div>
@endif