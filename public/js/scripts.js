
$(document).ready(function(){

    $("#print").click(function () {
        $('.invoice').printThis({
            // importStyle: true,
            importStyle: true,
        });
    });

    $("#printInvoice").click(function () {
        $('.modal-body .invoice').printThis({
            // importStyle: true,
            importStyle: true,
        });
    });

    $("#printReport").click(function () {
        $('.card .invoice').printThis({
            // importStyle: true,
            importStyle: true,
        });
    });
});
