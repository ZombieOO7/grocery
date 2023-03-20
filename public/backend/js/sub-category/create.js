$(document).ready(function() {
    /* Form validation */
    $("#m_form_1").validate({
        rules: {
            'title': {
                required: true,
                maxlength: rule.name_length,
                noSpace: true,
            },
            image: {
                required: function(element) {
                    if ($("#blah").attr('src') != "") {
                        return false;
                    } else {
                        return true;
                    }
                },
                extension: "jpg|jpeg|png"
            },
            status: {
                required: true,
            }
        },
        ignore: [],
        errorPlacement: function(error, element) {
            error.insertAfter(element);
        },
        invalidHandler: function(e, r) {
            $("#m_form_1_msg").removeClass("m--hide").show(),
                mUtil.scrollTop()
        },
        submitHandler: function(form) {
            if (!this.beenSubmitted) {
                this.beenSubmitted = true;
                form.submit();
            }
        },
    });

});


function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {

            $('#blah').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}
$("#imgInp").change(function() {
    $('#blah').show();
    readURL(this);
});