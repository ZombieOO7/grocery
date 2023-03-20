$(document).ready(function() {
    /* Form validation */
    $("#m_form_1").validate({
        rules: {
            title: {
                required: true,
                maxlength: rule.name_length,
                noSpace: true,
            },
            category_id: {
                required: true
            },
            sub_category_id: {
                required: true
            },
            'images[]': {
                required: function(element) {
                    if ($(".dynamicImages").children().length > 0) {
                        return false;
                    } else {
                        return true;
                    }
                },
                extension: "jpg|jpeg|png"
            },
            price: {
                required: true,
            },
            short_description: {
                required: true,
            },
            description: {
                cke_required: true,
            },
            status: {
                required: true,
            }
        },
        ignore: [],
        errorPlacement: function(error, element) {
            if (element.attr("name") == 'short_description') {
                error.insertAfter('.shortDescriptionError');
            } else if (element.attr("name") == 'description') {
                error.insertAfter('.descriptionError');
            } else if (element.attr("name") == 'sub_category_id') {
                error.insertAfter('.subCategoryId');
            } else if (element.attr("name") == 'category_id') {
                error.insertAfter('.categoryId');
            } else {
                error.insertAfter(element);
            }
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


function readURL(file) {
    var reader = new FileReader();
    reader.onload = function(e) {
        var html = '';
        html = `<div class='col-md-6'>
                    <img class="imgHeightWidth" src="${e.target.result}" alt="" style="display:block;" />
                </div>`;
        $('.dynamicImages').append(html);
        return;
    }
    reader.readAsDataURL(file);
    return;
}
$("#imgInp2").change(function() {
    $('.dynamicImages').html('');
    if (this.files && this.files.length > 0) {
        $('#blah').show();
        for (i = 0; i < this.files.length; i++) {
            readURL(this.files[i]);
        }
    }
});
jQuery.validator.addMethod("cke_required", function(value, element) {
    var idname = $(element).attr("id");
    var editor = CKEDITOR.instances[idname];
    $(element).val(editor.getData());
    return $(element).val().length > 0;
}, "This field is required");
$('#category_id').on('change', function() {
        $.ajax({
            url: getSubCat,
            method: 'POST',
            data: {
                category_id: this.value
            },
            success: function(response) {
                var select = document.getElementById('sub_category_id');
                $('#sub_category_id').find('option').remove().end().append('<option value="">Select</option>');
                response.list.forEach(item => {
                    var opt = document.createElement('option');
                    opt.value = item.id;
                    opt.innerHTML = item.title;
                    select.appendChild(opt);
                });
                $('#sub_category_id').selectpicker('refresh');
            },
            error: function(error) {

            }
        })
    })
    // CKEDITOR
CKEDITOR.on('instanceReady', function() {
    $.each(CKEDITOR.instances, function(instance) {
        CKEDITOR.instances[instance].document.on("keyup", CK_jQ);
        CKEDITOR.instances[instance].document.on("paste", CK_jQ);
        CKEDITOR.instances[instance].document.on("keypress", CK_jQ);
        CKEDITOR.instances[instance].document.on("blur", CK_jQ);
        CKEDITOR.instances[instance].document.on("change", CK_jQ);
    });
});

CKEDITOR.editorConfig = function(config) {
    config.allowedContent = true;
};

function CK_jQ() {
    for (instance in CKEDITOR.instances) {
        CKEDITOR.instances[instance].updateElement();
    }
}
CKEDITOR.replace('editor1');