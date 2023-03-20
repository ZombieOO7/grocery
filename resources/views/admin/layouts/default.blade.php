<!DOCTYPE html>
<html lang="en">
<!-- begin::Head -->

<head>
    <meta charset="utf-8" />
    <title>@yield('title') | {{ config('app.name', 'Laravel') }}</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/favicon@32x32.png') }}" />
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="csrfToken" id="csrfToken" content="{{ csrf_token() }}">
    @component('admin.includes.headerInc')
    @endcomponent
</head>
<!-- end::Head -->
<!-- begin::Body -->

<body
    class="m-page--fluid m--skin- m-content--skin-light2 m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--fixed m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default">
    <!-- begin:: Page -->
    <div class="m-grid m-grid--hor m-grid--root m-page">
        @component('admin.includes.header')
        @slot('title')
        @yield('title')
        @endslot
        @endcomponent

        <!-- begin::Body -->
        <div class="m-grid__item m-grid__item--fluid m-grid m-grid--ver-desktop m-grid--desktop m-body">
            @component('admin.includes.sidebar')
            @endcomponent
            @yield('content')
        </div>
        <!-- end:: Body -->
        @component('admin.includes.footer')
        @endcomponent
    </div>
    <!-- end:: Page -->
    <!-- begin::Scroll Top -->
    <div id="m_scroll_top" class="m-scroll-top">
        <i class="la la-arrow-up"></i>
    </div>
    <!-- end::Scroll Top -->
    @component('admin.includes.footerInc')
    @endcomponent
</body>
<!-- end::Body -->

</html>
