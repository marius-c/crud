<script>
    $.fn.dataTableExt.oPagination.semantic_buttons = {
        /*
         * Function: oPagination.four_button.fnInit
         * Purpose:  Initalise dom elements required for pagination with a list of the pages
         * Returns:  -
         * Inputs:   object:oSettings - dataTables settings object
         *           node:nPaging - the DIV which contains this pagination control
         *           function:fnCallbackDraw - draw function which must be called on update
         */
        "fnInit": function (oSettings, nPaging, fnCallbackDraw) {
            nFirst = $('<span onclick=""/>')[0];
            nLast = $('<span onclick=""/>')[0];

            nFirst.appendChild(document.createTextNode(oSettings.oLanguage.oPaginate.sFirst));
            nLast.appendChild(document.createTextNode(oSettings.oLanguage.oPaginate.sLast));

            nFirst.className = "item first";
            nLast.className = "item last";

            nPaging.appendChild(nFirst);

            window.oSettings = oSettings;

            nPaging.appendChild(nLast);


            $(nPaging).addClass('ui pagination menu');

            $(nFirst).click(function () {
                oSettings.oApi._fnPageChange(oSettings, "first");
                fnCallbackDraw(oSettings);
            });

            $(nLast).click(function () {
                oSettings.oApi._fnPageChange(oSettings, "last");
                fnCallbackDraw(oSettings);
            });

            /* Disallow text selection */
            $(nFirst).bind('selectstart', function () {
                return false;
            });
            $(nLast).bind('selectstart', function () {
                return false;
            });
        },

        /*
         * Function: oPagination.four_button.fnUpdate
         * Purpose:  Update the list of page buttons shows
         * Returns:  -
         * Inputs:   object:oSettings - dataTables settings object
         *           function:fnCallbackDraw - draw function which must be called on update
         */
        "fnUpdate": function (oSettings, fnCallbackDraw) {
            if (!oSettings.aanFeatures.p) {
                return;
            }


            /* Loop over each instance of the pager */
            var an = oSettings.aanFeatures.p;
            for (var i = 0, iLen = an.length; i < iLen; i++) {
                var buttons = an[i].getElementsByTagName('span');

                $(buttons[0]).parent().find('.item').not(':first-child').not(':last-child').remove();
                var pgTotal = Math.ceil(oSettings._iRecordsTotal / oSettings._iDisplayLength);
                var pgCurrent = Math.ceil(oSettings.fnDisplayEnd() / oSettings._iDisplayLength);
                var pgContainer = $(buttons[1]).parent();

                if (oSettings._iDisplayStart === 0) {
                    buttons[0].className = "item disabled";
                }
                else {
                    buttons[0].className = "item";
                }


                if (pgCurrent == pgTotal) {
                    buttons[1].className = "item disabled";
                }
                else {
                    buttons[1].className = "item";
                }

            }

            var last = function () {
                return pgContainer.find('.item').last().prev();
            };
            for (pgNum = 1; pgNum <= pgTotal; pgNum++) {

                if ((pgNum >= pgCurrent - 3 && pgNum <= pgCurrent + 3) || pgNum > (pgTotal - 3)) {

                    var button = $('<span onclick="" class="item"/>').text(pgNum);

                    last().after(button);

                    $(button).click(function () {
                        oSettings.oApi._fnPageChange(oSettings, parseInt($(this).text()) - 1);
                        fnCallbackDraw(oSettings);
                    });
                }
                else {
                    if (!last().hasClass('dots')) {
                        last().after('<div class="disabled dots item">...</div>');
                    }
                }
            }

            $(buttons).removeClass('active');
            for (j = 0; j < buttons.length; j++) {
                if (pgCurrent == parseInt($(buttons[j]).text())) {
                    $(buttons[j]).addClass('active');
                }
            }

        }
    };
</script>