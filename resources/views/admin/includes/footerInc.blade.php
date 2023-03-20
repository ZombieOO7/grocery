@php
$setting = getWebSetting();
@endphp
<!--begin::Base Scripts -->
<script src="{{ asset('backend/dist/default/assets/vendors/base/vendors.bundle.js') }}" type="text/javascript">
</script>
<script src="{{ asset('backend/dist/default/assets/demo/default/base/scripts.bundle.js') }}"
    type="text/javascript"></script>
<!--end::Base Scripts -->
<!--begin::Page Vendors -->
<!--end::Page Vendors -->
<!--begin::Page Snippets -->
<script src="{{ asset('backend/dist/default/assets/app/js/dashboard.js') }}" type="text/javascript"></script>
<!--end::Page Snippets -->

<script src="{{ asset('backend/dist/default/assets/vendors/custom/datatables/datatables.bundle.js') }}"
    type="text/javascript"></script>
<script src="{{ asset('js/jquery.raty.js') }}"></script>
<script src="{{ asset('backend/js/common.js') }}" type="text/javascript"></script>
@if(isset($setting) && $setting->notify == 1)
<script src="{{asset('js/pusher.min.js')}}"></script>
@endif
{{-- pusher notification config and data --}}
@php
    // $updateNotification = route('notification.update');
    // $appKey = env('PUSHER_APP_KEY');
    // $cluster = env('PUSHER_APP_CLUSTER');
@endphp
<script>
//Ajax Loader
// var updateNotification = ;
// var appKey = ;
// var cluster = ;
$(document).ready(function() {
    $(document).ajaxStart(function() {
        $(document).find('.main_loader').show();
    }).ajaxStop(function() {
        $(document).find('.fixedStar').raty({
            readOnly: true,
            path: base_url + '/images',
            starOff: 'small-star-off.png',
            starOn: 'small-star-on.png',
            half: true,
            start: $(document).find(this).attr('data-score')
        });
        $(document).find('.main_loader').hide();
    });
});
/* notification bell */

/*  */

@if(isset($setting) && $setting->notify == 1)
@endif
</script>
<script src="{{ asset('sweetalert/sweetalert.min.js') }}"></script>
<script type="text/javascript" src="{{ str_replace('public/', '', URL('resources/lang/js/en/message.js')) }}"></script>
@yield('inc_script')
@component('vendor.sweetalert.view')
@endcomponent
