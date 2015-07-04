
<style>
    .advanced-search-container .search-padded {
        padding: 15px 10px 10px;
    }

    .advanced-search-container .submit-search {
        float:right;
        margin-top: 10px;
    }
</style>

<div class="ui segment advanced-search-container" style="display:none; padding:0">
    <div class="ui dividing header">
        <div class="search-padded">Advanced search</div>
    </div>

    <form class="ui form search-padded">
        @foreach($crud->columns->dbable() as $column)
            <?php
            $column = clone $column;
            $column->default = null;
            ?>
            @if($column->search)
                @include('themes.semantic.inc.form.search-field')
            @endif
        @endforeach

        <button advanced-seach-submitter class="ui blue button submit-search" >Search</button>
        <div class="clearfix"></div>
    </form>
</div>


@section('js')
    <script>
        (function() {
            var container = $('.advanced-search-container'),
                filtersAreActive = false,
                launcher = $('[advanced-search-launcher]'),
                submitter = $('[advanced-seach-submitter]');

            container.find('input').change(function(){
                filtersAreActive = true;
            });


            function submitAdvancedSearch()
            {
                var table = $('{{$crud->selector}}').dataTable();
                table._fnReDraw();
            }

            function toggleAdvancedSearch()
            {
                if(container.is(':visible')) {
                    submitAdvancedSearch();
                }
                container.slideToggle(300);

                if(filtersAreActive) {
                    launcher.addClass('green active');
                }
            }
            launcher.click(toggleAdvancedSearch);
            submitter.click(toggleAdvancedSearch);
        })();
    </script>
    @yield('js')
@overwrite