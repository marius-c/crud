@if($column->max_length)
    Remaining characters: <span class="length{{$column->name}}">{{$column->max_length}}</span>
    <script>
        (function () {
            var info = $('.length{{$column->name}}');
            var max = {{$column->max_length}};
            $('[name={{$column->name}}]').keyup(function () {
                info.text(max - $(this).val().length);
            }).keyup();
        })();
    </script>
@endif
