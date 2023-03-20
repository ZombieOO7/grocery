$(document).ready(function () {
    /* Form validation */
    if($("#m_form_1").length > 0){
        $("#m_form_1").validate({
            rules: {
                first_name: {
                    required: true,
                    maxlength: rule.name_length,
                    noSpace: true,
                },
                last_name: {
                    required: true,
                    maxlength: rule.name_length,
                    noSpace: true,
                },
                email: {
                    required: true,
                    email: true,
                    maxlength: rule.email_length,
                    noSpace: true,
                },
                password: {
                    required: function (element) {
                        if ($("#id").val().length > 0) {
                            return false;
                        } else {
                            return true;
                        }
                    },
                    minlength: rule.password_min_length,
                    maxlength: rule.password_max_length,
                },
                confirm_password: {
                    required: function (element) {
                        if ($("#id").val().length > 0) {
                            return false;
                        } else {
                            return true;
                        }
                    },
                    minlength: rule.password_min_length,
                    maxlength: rule.password_max_length,
                    equalTo: "#password",
                },
                phone: {
                    required: true,
                    number: true,
                    maxlength: rule.phone_length,
                    noSpace: true,
                },
                image: {
                    required: function (element) {
                        if ($("#blah").attr('src') != "") {
                            return false;
                        } else {
                            return true;
                        }
                    },
                    extension: "jpg|jpeg|png"
                },
                company_id: {
                    required: true,
                },
                user_type: {
                    required: true,
                },
                role_id: {
                    required: true,
                },
                status: {
                    required: true,
                }
            },
            messages: {
                'role[]': "Please select at least 1 role",
                confirm_password: {
                    required: 'Please enter confirm password',
                    equalTo: 'The confirm password and password must match.'
                },
                password:{
                    required: 'Please enter password',
                }
            },
            ignore: [],
            errorPlacement: function (error, element) {
                console.log(element.attr("name"));
                if (element.attr("name") == "password")
                    error.insertAfter(".passwordError");
                else if (element.attr("name") == "confirm_password")
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
    }
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#blah').attr('src', e.target.result);
                $('#blah').css('display', 'block');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#imgInput").change(function () {
        readURL(this);
    });
});
$(document).on('click', '.deleteUser', function () {
    var id = $(this).attr('id');
    checkJob(jobCheckUrl,id);
    var url = $(this).attr('data-url');
    var tableName = $(this).attr('data-table_name');
    swal({
        title: 'Are you sure?',
        text: 'Once deleted, you will not be able to recover this data!',
        icon: "warning",
        buttons: true,
        dangerMode: true,
        closeOnClickOutside: false,
    }).then((willDelete) => {
        if (willDelete) {
            $.ajax({
                url: url,
                method: "delete",
                data: {
                    id: id
                },
                success: function (response) {
                    window.location.replace(returnUrl);
                }
            })
        }
    });
});
$(document).on('click', '.active_inactive_user', function () {
    var id = $(this).attr('id');
    checkJob(jobCheckUrl,id);
    var url = $(this).attr('data-url');
    var tableName = $(this).attr('data-table_name');
    var status = $(this).attr('data-status');
    swal({
        title: 'Are you sure?',
        text: 'You want to change status!',
        icon: "warning",
        buttons: true,
        dangerMode: true,
        closeOnClickOutside: false,
    }).then((isConfirm) => {
        if (isConfirm) {
            $.ajax({
                url: url,
                method: "POST",
                data: {
                    id: id,
                    status: status
                },
                success: function (response) {
                    if (status == 0) {
                        $('#tggl-clss').removeClass('fas fa-toggle-off');
                        $('#tggl-clss').addClass('fas fa-toggle-on');
                        $('.userStatus').attr('data-status', '1');
                    } else {
                        $('#tggl-clss').removeClass('fas fa-toggle-on');
                        $('#tggl-clss').addClass('fas fa-toggle-off');
                        $('.userStatus').attr('data-status', '0');
                    }
                    swal(response['msg'], {
                        icon: response['icon'],
                        closeOnClickOutside: false,
                    });
                }
            })
        }
    });
});
$(document).on('change', '#user_type', function () {
    var id = $(this).attr('data-id');
    var url = $(this).attr('data-url');
    var type = $(this).val();
    if(type != null && type != ''){
        swal({
            title: 'Are you sure?',
            text: 'You want to change position!',
            icon: "warning",
            buttons: true,
            dangerMode: true,
            closeOnClickOutside: false,
        }).then((isConfirm) => {
            if (isConfirm) {
                $.ajax({
                    url: url,
                    method: "POST",
                    data: {
                        id: id,
                        userType: type
                    },
                    success: function (response) {
                        swal(response['msg'], {
                            icon: response['icon'],
                            closeOnClickOutside: false,
                        });
                        // getRoleList(type);
                    }
                })
            }
        });
    }
});
$(document).on('change', '#positionId', function () {
    var type = $(this).val();
    getRoleList(type);
});
function getRoleList(type){
    $.ajax({
        url: getRoleUrl,
        method: "GET",
        global: false,
        data: {
            id: type
        },
        success: function (response) {
            $('#roleId').find('option').remove();
                $('#roleId').append($("<option></option>")
                                .attr("value",'')
                                .text('Select Role'));
                $.each(response.roles, function(key, value) {
                    $('#roleId').append($("<option></option>")
                                    .attr("value",key)
                                    .text(value));
                });
        }
    });
}
$('#roleId').prepend($("<option></option>")
            .attr("value",'')
            .text('Select Role'));

// $('#roleId').on('change', function(){
//     var val = $('#roleId').val();
//     var text = 'You want to change user role!';
//     if(val != null || val == '' ){
//         changeRole(text,val);
//     }
// })

// function changeRole(text,val){
//     swal({
//         title: 'Are you sure?',
//         text: text,
//         icon: "warning",
//         buttons: true,
//         dangerMode: true,
//         closeOnClickOutside: false,
//     }).then((isConfirm) => {
//         $.ajax({
//             url: url,
//             method: "POST",
//             data: {
//                 id: id,
//                 userType: type
//             },
//             success: function (response) {
//                 swal(response['msg'], {
//                     icon: response['icon'],
//                     closeOnClickOutside: false,
//                 });
//             }
//         })
//     })
// }