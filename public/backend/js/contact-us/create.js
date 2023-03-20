$(document).ready(function () {
    var keycodes = {
        'backspace': 8,
        'plus': 43,
        'dash': 45,
        'delete': 46,
        'leftArrow': 37,
        'rightArrow': 39,
        'number1': 48,
        'number9': 57
    };
    function noNumbers(e) {
        var charCode = e.which ? e.which :
            (e.charCode ? e.charCode :
                (e.keyCode ? e.keyCode : 0));
        if ((charCode < keycodes.number1 || charCode > keycodes.number9) &&
            charCode !== keycodes.delete &&
            charCode !== keycodes.plus &&
            charCode !== keycodes.dash &&
            charCode !== keycodes.backspace &&
            charCode !== keycodes.leftArrow &&
            charCode !== keycodes.rightArrow)
            e.preventDefault();
    }
    document.getElementById('phone').addEventListener(
        'keypress', noNumbers
    );
    $("#m_form_1").validate({
        rules: {
            full_name: {
                required: true,
                noSpace: true,
            },
            email: {
                required: true,
                noSpace: true,
            },
            subject: {
                required: true,
                noSpace: true,
            },
            phone: {
                required: true,
                minlength: 8,
                maxlength: 13,
                noSpace: true,
            },
            message: {
                required: true,
                noSpace: true,
            },
            status: {
                required: true,
            }
        },
        ignore: [],
        errorPlacement: function (error, element) {
            error.insertAfter(element);
        },
        invalidHandler: function (e, r) {
            $("#m_form_1_msg").removeClass("m--hide").show(),
                mUtil.scrollTop()
        },
    });
}); 