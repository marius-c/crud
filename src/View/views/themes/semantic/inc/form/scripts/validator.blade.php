@extends('global.inc.form.scripts.validator')

@section('callback')
    <script>
        function displayFormErrors(response) {
            $('.error-message').remove();
            $('.crud-error').each(function() {
                $(this).removeClass('error crud-error');
            });

            for(column in response.messages) {
                console.log('[name='+column+'], [name="'+column+'[]"]', $('[name='+column+'], [name="'+column+'[]"]'));
                var field = $('[name='+column+'], [name="'+column+'[]"]').closest('.field');
                field.addClass('error crud-error');

                for(i in response.messages[column]){
                    field.append('<div class="ui error-message red pointing prompt label transition visible">'+response.messages[column][i]+'</div>');
                }
            }
        }
    </script>
@stop
