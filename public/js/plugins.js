// Fungsi untuk menginisialisasi plugin
$(document).ready(function () {
    // tooltip
    $("tbody").tooltip({
        selector: '[data-bs-tooltip="tooltip"]'
    });
    
    // flatpickr
    flatpickr(".datepicker", {
        locale: "id",
        altInput: true,
        altFormat: "j F Y",
        dateFormat: "Y-m-d",
        disableMobile: "true"
    });
    /* Documentation : https://flatpickr.js.org/ */
    
    // select2
    $('.select2-single').each(function () {
        $(this).select2({
            // fix select2 search input focus bug
            dropdownParent: $(this).parent(),
        })
    })

    // fix select2 bootstrap modal scroll bug
    $(document).on('select2:close', '.select2-single', function (e) {
        var evt = "scroll.select2"
        $(e.target).parents().off(evt)
        $(window).off(evt)
    })
});