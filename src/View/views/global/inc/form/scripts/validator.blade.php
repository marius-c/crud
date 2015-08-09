@if($crud->options['rules'])
    <script>
        window.forceSubmit = false;

        $('.ui.form').submit(function (e) {
            if (forceSubmit) return true;

            var button = $('[crud-action=save]');
            button.addClass('loading');

            $.ajax({
                url: '{!!$crud->router["validate"]->url()!!}',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.fails) {
                        displayFormErrors(response);

                        button.removeClass('loading');

                    } else {
                        forceSubmit = true;
                        $('.ui.form').submit();
                    }
                }
            });

            return false;
        });

        $(document).ajaxError(function (e, xhr, ajaxSettings) {
            if (/crudAttachmentsUpload/.test(ajaxSettings.url)) {
                if (xhr.status == 422) {
                    displayFormErrors({messages: xhr.responseJSON});
                } else {
                    clearValidationErrors();
                }
            }
        });
    </script>

    @yield('callback')
@endif