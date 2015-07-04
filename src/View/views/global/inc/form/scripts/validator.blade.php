@if($crud->options['rules'])
    <script>
        window.forceSubmit = false;

        $('.ui.form').submit(function(e){
            if(forceSubmit) return true;

            var button = $('[crud-action=save]');
            button.addClass('loading');


            $.ajax({
                url: '{!!$crud->router["validate"]->url()!!}',
                data: $(this).serialize(),
                success: function(response) {
                    if(response.fails) {
                        displayFormErrors(response);

                        button.removeClass('loading');

                    } else{
                        forceSubmit = true;
                        $('.ui.form').submit();
                    }
                }
            });

            return false;
        });
    </script>

    @yield('callback')
@endif