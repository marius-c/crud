<style>
    .crud-container {
        padding: 5px;
    }

    body {
        background: #f7f7f7 !important;
    }

    .crud th {
        cursor: pointer !important;
    }

    .clearfix {
        clear: both;
    }

    .table .button {
        padding: .6em 1em !important;
    }

    .expandable.expandable {
        display: block;
        text-align: center !important;
        padding: .5em 1.7em !important;
    }

    .search-filters input {
        padding: .47861em .6em !important;;
    }

    .search-filters .ui.dropdown {
        padding: .62861em .6em !important;;
    }

    .highlight {
        color: rgb(219, 0, 255);
    }

    .table td {
        border-bottom-width: 0 !important;
    }

    .dataTables_length, .dataTables_filter {
        display: none;
    }

    [onclick] {
        cursor: pointer;
    }

    .chosen-container {
        width: 100% !important;
        background: white !important;
    }

    .chosen-single.chosen-single.chosen-single, .chosen-choices.chosen-choices.chosen-choices {
        padding: .65em 1em;
        height: auto;
        border-color: rgba(0, 0, 0, .15);
        background: none !important;
        border-radius: .3125em !important;
        box-shadow: none;
    }

    .field.error .chosen-single.chosen-single.chosen-single, .field.error .chosen-choices.chosen-choices.chosen-choices {
        background-color: snow;
        border-color: #E7BEBE;
        border-left: none;
        color: #D95C5C;
        padding-left: 1.2em;
        border-bottom-left-radius: 0 !important;
        border-top-left-radius: 0 !important;
        -webkit-box-shadow: .3em 0 0 0 #D95C5C inset;
        box-shadow: .3em 0 0 0 #D95C5C inset;
    }

    .chosen-container-single .chosen-single, .chosen-choices-single .chosen-choices {
        line-height: calc(2.7em - 1.65em + .3em) !important;
    }

    .chosen-single div b, .chosen-choices div b {
        margin-top: 8px !important;
    }

    .tagsinput {
        width: 100% !important;
        height: 50px !important;
    }

    .table tr td:last-child {
        white-space: nowrap;
    }

    {!!Ionut\Crud\Application::app('config')->get('style.css')!!}
</style>