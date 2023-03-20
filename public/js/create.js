$(document).ready(function () {
    $("#m_form_1").validate({
        rules: {
            title: {
                required: true
            },
            description: {
                required: true
            },
            image: {
                required: true
            },
            status: {
                required: true
            },
        },
        ignore: [],
        errorPlacement: function (error, element) {
            if (element.attr("name") == "page_content")
                error.insertAfter(".contentError");
            else
                error.insertAfter(element);
        },
        invalidHandler: function (e, r) {
            $("#m_form_1_msg").removeClass("m--hide").show(),
                mUtil.scrollTop()
        },
    });
}); 