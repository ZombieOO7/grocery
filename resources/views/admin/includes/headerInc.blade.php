<!--begin::Web font -->
<script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
<script>
WebFont.load({
    google: {
        "families": ["Poppins:300,400,500,600,700", "Roboto:300,400,500,600,700"]
    },
    active: function() {
        sessionStorage.fonts = true;
    }
});
</script>
<!--end::Web font -->
<!--begin::Base Styles -->
<!--begin::Page Vendors -->

<!--end::Page Vendors -->
<link href="{{ asset('backend/dist/default/assets/vendors/base/vendors.bundle.css') }}" rel="stylesheet"
    type="text/css" />
<link href="{{ asset('backend/dist/default/assets/demo/default/base/style.bundle.css') }}" rel="stylesheet"
    type="text/css" />

<link href="{{ asset('backend/css/common.css') }}" rel="stylesheet" type="text/css" />
<!--end::Base Styles -->
<?php /*<link rel="shortcut icon" href="{{ asset('backend/dist/default/assets/demo/default/media/img/logo/favicon.ico') }}" />*/?>
<link href="{{ asset('backend/dist/default/assets/vendors/custom/datatables/datatables.bundle.css') }}"
    rel="stylesheet" type="text/css" />
<script type="text/javascript">
var base_url = '<?php echo URL('/'); ?>';
</script>
@yield('inc_css')
