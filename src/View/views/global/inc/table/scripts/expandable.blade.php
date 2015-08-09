<script>
    function attachExpandableEvent() {
        $(document).on('click', 'a.expandable', function (e) {
            var tr = $(this).closest('tr');
            var url = $(this).attr('data-target');

            if (tr.next('tr').is('.expanded')) {
                $(this).find('i').removeClass('fa-minus-square').addClass('fa-plus-square');
                tr.next('tr').find('div:first').slideToggle('fast', function () {

                    tr.next('tr').remove();
                });
                return;
            } else {
                $(this).find('i').removeClass('fa-plus-square').addClass('fa-minus-square');
            }

            var expanded = $('<tr class="expanded">').html('<td colspan="50" style="max-width:1000px;"><div></div></td>');
            tr.after(expanded);
            expanded = expanded.find('div');

            var loader = '<div class="ui active inverted dimmer"><div class="ui text loader">Loading</div></div>';
            expanded.html('<div class="ui segment" style="min-height: 70px;">' + loader + '</div>');
            expanded.slideDown();

            var expandableType = $(this).data('expandable-type');
            if (expandableType == 'normal') {
                $.ajax({
                    url: url,
                    success: function (html) {
                        expanded.slideDown(function () {
                            expanded.find('.segment').html(html);
                            expanded.slideDown();
                        });
                    }
                });
            }
            else if (expandableType == 'iframe') {
                var iframe = $('<iframe scrolling="no" frameborder="0" style="width:100%; height:10px; display:none" src="' + url + '"/>');
                var segment = expanded.find('.segment');
                $(segment).append(iframe);
                iframe.load(function () {
                    expanded.slideDown(function () {
                        expanded.slideDown();
                        $(iframe).show();
                        segment.find('.dimmer').remove();
                    });

                    setInterval(function () {
                        iframe.height(iframe.contents().height());
                    }, 100);
                });

            }
        });
    }
    attachExpandableEvent();
    afterDatatablesCallbacks.push(attachExpandableEvent);
</script>