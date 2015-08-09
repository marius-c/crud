<script>
    $(document).on('click', '[crud-action][confirm=1]', function (e) {
        var confirmed = confirm("Are you sure?");
        $(this).data('confirmed', confirmed ? 'true' : 'false');

        return confirmed;
    });
</script>