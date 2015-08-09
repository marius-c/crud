<script>
    $(document).on('click', '[crud-action][ajax=1]', function () {
        if ($(this).data('confirmed') !== 'false') {
            var action = $(this);
            action.addClass('loading');

            $.ajax({
                url: this.href,
                success: function (response) {
                    action.closest('td').html(response);
                    action.removeClass('loading');
                }
            });
        }

        return false;
    });
</script>