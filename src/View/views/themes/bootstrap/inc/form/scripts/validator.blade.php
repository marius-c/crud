@extends('global.inc.form.scripts.validator')

@section('callback')
    <script>
        function displayFormErrors(response) {
            $('.error-message').remove();
            for(column in response.messages) {
                var field = $('[name='+column+']').closest('.field');
                field.addClass('error');

                for(i in response.messages[column]){
                    field.append('<div class="ui error-message red pointing prompt label transition visible">'+response.messages[column][i]+'</div>');
                }
            }
        }
    </script>
@stop
