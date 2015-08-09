<script>
    // Apply the columns search
    $('.search-filters').find('input, select').on('change keyup', function () {
        var s = $(this).val();
        if ($(this).attr('type') == 'checkbox')
            s = $(this).is(':checked') ? 1 : 0;

        var interval = $(this).attr('interval');
        if (interval) {
            console.log('[name="' + interval + '[from]"]');
            s = $('[name="' + interval + '[from]"]').val() + '-:-' + $('[name="' + interval + '[to]"]').val();
        }

        datatable
                .column($('.search-filters td').index($(this).closest('td')))
                .search(s)
                .draw();
    });
</script>