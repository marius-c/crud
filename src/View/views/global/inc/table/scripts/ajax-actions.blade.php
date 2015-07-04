<script>
    $(document).on('click', '[crud-action][ajax=1]', function(){
        var a = $(this);
        a.addClass('loading');
        $.ajax({
            url: this.href,
            success: function(response) {
                a.closest('td').html(response);
                a.removeClass('loading');
            }
        });

        return false;
    });
</script>