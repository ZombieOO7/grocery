$(document).ready(function () {
    /* Form Validation */
    $("#m_form_1").validate({
        rules: {
            title: {
                required: true,
                maxlength: rule.name_length,
                noSpace: true,
            },
            subject: {
                required: true,
                maxlength: rule.name_length,
                noSpace: true,
            },
            body: {
                required: true,
                noSpace: true,
            }
        },
        
        ignore: [],
        errorPlacement: function (error, element) {
            if (element.attr("name") == "body")
                error.insertAfter(".contentError");
            else
                error.insertAfter(element);
        },
        invalidHandler: function (e, r) {
            $("#m_form_1_msg").removeClass("m--hide").show(),
                mUtil.scrollTop()
        },
        submitHandler: function (form) {
            // Prevent double submission
            if (!this.beenSubmitted) {
                this.beenSubmitted = true;
                form.submit();
            }
        },
    });
}); 

//deal with copying the ckeditor text into the actual textarea
CKEDITOR.on('instanceReady', function () {
    $.each(CKEDITOR.instances, function (instance) {
        CKEDITOR.instances[instance].document.on("keyup", CK_jQ);
        CKEDITOR.instances[instance].document.on("paste", CK_jQ);
        CKEDITOR.instances[instance].document.on("keypress", CK_jQ);
        CKEDITOR.instances[instance].document.on("blur", CK_jQ);
        CKEDITOR.instances[instance].document.on("change", CK_jQ);
    });
});

function CK_jQ() {
    for (instance in CKEDITOR.instances) {
        CKEDITOR.instances[instance].updateElement();
    }
}

CKEDITOR.replace('editor1');

$('#variables a').click(function () {
    var str = $(this).text();
    CKEDITOR.instances['editor1'].insertHtml(str);
});