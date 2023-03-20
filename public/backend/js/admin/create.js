$(document).ready(function () {

    $("#m_form_1").validate({
        rules: {
            first_name: {
                required: true,
                noSpace: true,
                maxlength:rule.name_length,
            },
            last_name: {
                required: true,
                noSpace: true,
                maxlength:rule.name_length,
            },
            email: {
                required: true,
                email: true,
                noSpace: true,
                maxlength:rule.email_length,
            },
            password: {
                required: function (element) {
                    if ($("#id").val().length > 0) {
                        return false;
                    } else {
                        return true;
                    }
                },
                minlength: 6,
                maxlength: 16,
                noSpace: true,
            },
            conform_password: {
                required: function (element) {
                    if ($("#id").val().length > 0) {
                        return false;
                    } else {
                        return true;
                    }
                },
                minlength: 6,
                maxlength: 16,
                equalTo: "#password",
                noSpace: true,
            },
            "role[]": {
                required: true,
            },
            status: {
                required: true,
            }
        },
        messages: {
            'role[]': {
                required:"Please select at least 1 role",
            },
            email: {
                required: 'Please enter valid email & it should be like xyz@example.com'
            }

        },
        ignore: [],
        errorPlacement: function (error, element) {
            if (element.attr("name") == "password")
                error.insertAfter(".passwordError");
            else if (element.attr("name") == "conform_password")
                error.insertAfter(".conformPasswordError");
            else if (element.attr("name") == "role[]")
                error.insertAfter(".roleError");
            else
                error.insertAfter(element);
        },
        invalidHandler: function (e, r) {
            $("#m_form_1_msg").removeClass("m--hide").show(),
                mUtil.scrollTop()
        },
    });
});