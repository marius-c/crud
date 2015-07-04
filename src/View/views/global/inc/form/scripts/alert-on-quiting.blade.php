<script>
    window.userChangedSomething = false;
    $('.crud-form').find('input, select').change(function() {
        window.userChangedSomething = true;
    });

    $('.crud-form').submit(function() {
        window.userChangedSomething = false;
    });

    window.onbeforeunload = function() {
        if(userChangedSomething) {
            return "Your data isn't saved. Leaving the page will erase the changes.";
        }
    };
</script>