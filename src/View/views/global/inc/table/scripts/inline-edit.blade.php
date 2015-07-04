<script>

    @if(isset($crud->actions['edit']))
        function saveInline(input)
        {
            var row_id = $(input).closest('tr').attr('id').replace('row_', '');

            var data = {
                row_id: row_id,
                inline: $(input).attr('name')
            };
            if($(input).attr('type') == 'checkbox') {
                if($(input).is(':checked'))
                    data[$(input).attr('name')] = 'on';
            }
            else {
                data[$(input).attr('name')] = $(input).val();
            }

            $.ajax({
                'url': '{!!$crud->actions['save']->url()!!}',
                'data': data,
                'method': 'post'
            });
        }

        $(document).on('change', '.inline-container input[type=checkbox]', function(){
            saveInline(this);
        });

        $(document).on('keyup', '.inline-container input[type=text]', function(e) {
            if(e.keyCode == 13) {
                saveInline(this);
            }
        });
    @endif
</script>