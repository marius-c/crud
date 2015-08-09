$('.ui.checkbox').checkbox();
$('.multiselect, .chosen').chosen(); // must be before dropdown
$('.form select').not('.multiselect, .chosen').dropdown();
$('.tags').tagsInput({defaultText: "Add one.."});

$('.datetimepicker').datetimepicker({
format: 'Y-m-d H:i:s'
});

$('.datepicker').datetimepicker({
format: 'Y-m-d',
timepicker: false
});