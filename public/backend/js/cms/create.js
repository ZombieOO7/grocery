$(document).ready(function () {

    /* Form Validation */
    $("#m_form_1").validate({
        rules: {
            page_title: {
                required: true,
                maxlength:rule.name_length,
                noSpace: true,
            },
            page_content: {
                required: true,
                // maxlength:rule.content_length,
                noSpace: true,
            },
            meta_keyword:{
                maxlength:rule.name_length,
            },
            meta_description: {
                maxlength:rule.content_length,
            },
            status: {
                required: true,
            }
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