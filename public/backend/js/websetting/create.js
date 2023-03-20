$(document).ready(function () {
    $.validator.addMethod('filesize', function (value, element, param) {
        return this.optional(element) || (element.files[0].size <= param)
    }, 'File size must be less than {0}');
    
    $(document).find("#web_general_setting_form").validate({
        rules:{
            logo:{
                extension:rule.logo_extension,
                filesize: rule.logo_size
            },
            favicon:{
                extension: rule.favicon_extension,
                filesize:rule.favicon_size
            },
        },
        ignore: [],
        errorPlacement: function (error, element) {

            error.insertAfter(element);
        },
        invalidHandler: function (e, r) {
            debugger;
            $("#web_general_form_msg").removeClass("m--hide").show(),
                mUtil.scrollTop()
                e.preventDefault();

        },
    });
    $(document).find("#socila_media_link_form").validate({
        rules: {
            facebook_url: {
                url: rule.url
            },
            google_url:{
                url: rule.url
            },
            youtube_url:{
                url: rule.url
            },
            twitter_url:{
                url: rule.url
            }
        },
        ignore: [],
        errorPlacement: function (error, element) {

            error.insertAfter(element);
        },
        invalidHandler: function (e, r) {
            $("#web_social_media_link_msg").removeClass("m--hide").show(),
                mUtil.scrollTop()
        },
    });

    $(document).on('change','input[name=logo]',function () {
        filePreview(this,'logoImg');
    });
    $(document).on('change','input[name=favicon]',function () {
        filePreview(this,'faviconImg');
    });
    $(document).on('change','input[name=android_app_icon]',function () {
        filePreview(this,'android_app_icon_Img');
    });
    $(document).on('change','input[name=ios_app_icon]',function () {
        filePreview(this,'ios_app_icon_Img');
    });
    
    function filePreview(input,id) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#'+id).empty();
                $('#'+id).append('<img src="'+e.target.result+'" style="width:100px;height:100px;"/>');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    $(document).find("#web_general_setting_form").validate({
        rules:{
            logo:{
                extension:rule.logo_extension,
                filesize: rule.logo_size
            },
            favicon:{
                extension: rule.favicon_extension,
                filesize:rule.favicon_size
            },
        },
        ignore: [],
        errorPlacement: function (error, element) {

            error.insertAfter(element);
        },
        invalidHandler: function (e, r) {
            debugger;
            $("#web_general_form_msg").removeClass("m--hide").show(),
                mUtil.scrollTop()
                e.preventDefault();

        },
    });

    // $(document).find("#push_notification_setting_tab_id").validate({
    //     rules:{
    //         android_app_icon:{
    //             extension: "jpg|jpeg|png",
    //         },
    //         ios_app_icon:{
    //             extension: "jpg|jpeg|png",
    //         },
    //     },
    //     ignore: [],
    //     errorPlacement: function (error, element) {
    //         console.log(error);
    //         error.insertAfter(element);
    //     },
    //     invalidHandler: function (e, r) {
    //         debugger;
    //         $("#push_notification_link_msg").removeClass("m--hide").show(),
    //             mUtil.scrollTop()
    //             e.preventDefault();

    //     },
    // });
}); 