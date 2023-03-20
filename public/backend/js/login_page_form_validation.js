var FormControls={
    init:function(){$("#m_form_1").validate
    (
        {
            rules:{
                email:{
                    required:true,
                    minlength:10,
                    maxlength: rule.email_max_length,
                },
                password:{
                    required:true,
                    minlength: rule.password_min_length,
                    maxlength: rule.password_max_length,
                },
                password_confirmation: {
                    required:true,
                    minlength: rule.password_min_length,
                    maxlength: rule.password_max_length,
                    equalTo: "#password",
                },
            },
            messages: {
               email: {
                    required: 'Please enter valid email & it should be like xyz@example.com'
                },password: {
                    required: 'Please enter password'
                },password_confirmation: {
                    required: 'Please enter confirm password',
                    equalTo: 'The confirm password and password must match.'
                }

            }
        }
    )
    }
};
jQuery(document).ready(function(){FormControls.init()}
);