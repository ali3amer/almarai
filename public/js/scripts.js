
$(document).ready(function(){

    $('input').attr('autocomplete','off');

    $("#print").click(function () {
        $('.invoice').printThis({
            // importStyle: true,
            importStyle: true,
        });
    });
});
