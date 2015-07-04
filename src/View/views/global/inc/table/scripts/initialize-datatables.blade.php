<script>
    @if(isset($GLOBALS['preloaded_by_iframe']))
        window.datatablesPreload = {!!htmlspecialchars($crud->preload(), ENT_QUOTES)!!};
    @else
        window.datatablesPreload = {!!$crud->preload()!!};
    @endif
    window.datatablesNeedPreload = true;
    window.table = $(crudSelector).dataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": {
            beforeSend: function(xhr, settings){
                if(datatablesNeedPreload){
                    settings.success(datatablesPreload);
                    datatablesNeedPreload = false;
                    return false;
                }

                var serializedSearch = $('.advanced-search-container form').serialize();
                this.url = this.url+'&search='+encodeURIComponent(serializedSearch);
            },
            "url": "{!!$crud->router['processing']->url($crud->filters->query())!!}",
            "type": "POST"
        },
        "fnDrawCallback": function() {
            @include('global.inc.form.scripts.initialize-fields')

            for(i in afterDatatablesCallbacks) {
                afterDatatablesCallbacks[i]();
            }
        },
        "order": [[ {{$crud->options['default_order_column']}}, "{{$crud->options['default_order_type']}}" ]],
        "sPaginationType": "semantic_buttons"
    } );
    window.datatable = table.DataTable();
</script>