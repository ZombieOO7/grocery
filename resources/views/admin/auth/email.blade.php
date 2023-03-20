<!DOCTYPE html>
<html lang="en">
<!-- begin::Head -->

<head>
    <meta charset="utf-8" />
    <title>Admin Reset Password | {{ config('app.name', 'Laravel') }}</title>
    <link rel="shortcut icon" type="image/png" href="{{ URL::asset('frontend/images/favicon.png') }}" />
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
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

    function trim(el) {
        el.value = el.value.
        replace(/(^\s*)|(\s*$)/gi, ""). // removes leading and trailing spaces
        replace(/[ ]{2,}/gi, " "). // replaces multiple spaces with one space
        replace(/\n +/, "\n"); // Removes spaces after newlines
        return;
    }
    </script>
    <!--end::Web font -->
    <!--begin::Base Styles -->
    <link href="{{ asset('backend/dist/default/assets/vendors/base/vendors.bundle.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('backend/dist/default/assets/demo/default/base/style.bundle.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('backend/css/common.css') }}" rel="stylesheet" type="text/css" />
    <style type="text/css">
        .alert .close:before {display: none;}
        .alert .close{
            padding-top: 0;
            font-size: 23px;
        }
    </style>

</head>
<!-- end::Head -->
<!-- begin::Body -->

<body
    class="m--skin- m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--fixed m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default">
    <!-- begin:: Page -->
    <div class="m-grid m-grid--hor m-grid--root m-page">
        <div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor m-login m-login--signin m-login--2 m-login-2--skin-2"
            id="m_login"
            style="background-image: url({{ asset('images/bg-3.jpg') }});">
            <div class="m-grid__item m-grid__item--fluid m-login__wrapper">
                <div class="m-login__container">
                    <div class="m-login__logo">
                        <a href="{{ URL('/admin') }}">
                            <img src="{{ asset('images/favicon@64x64.png') }}" alt="{{ env('APP_NAME') }}" width="70" height="70">
                        </a>
                    </div>
                    <div class="m-login__signin">
                        <div class="m-login__head">
                            <h3 class="m-login__title">{{ __('Admin Reset Password') }}</h3>
                        </div>
                        @include('admin.includes.flashMessages')
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <form method="post" action="{{ url('admin/password/email') }}" aria-label="{{ __('Login') }}"
                            class="login-form m-login__form m-form" id="m_form_1">
                            @csrf
                            <div class="form-group m-form__group">
                                <input id="email" width="70" height="70" type="email" maxlength="{{config('constant.email_length')}}" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="m-login__form-action">
                                <button type="submit" id="m_login_signin_submit"
                                        class="btn btn-focus m-btn m-btn--pill m-btn--custom m-btn--air  m-login__btn m-login__btn--primary">{{ __('Send Password Reset Link') }}</button>
                            
                            </div>
                            <div style="text-align:center;">
                                    <a href="{{ url('/admin') }}" class="back-login">
                                        <span>Back to Login</span>
                                    </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- end:: Page -->
    <!--begin::Base Scripts -->
    <script src="{{ asset('backend/dist/default/assets/vendors/base/vendors.bundle.js') }}" type="text/javascript">
    </script>
    <script src="{{ asset('backend/dist/default/assets/demo/default/base/scripts.bundle.js') }}"
        type="text/javascript"></script>
    <script>
        var rule = $.extend({}, {!!json_encode(config('constant'), JSON_FORCE_OBJECT) !!});
    </script>
    <script src="{{ asset('backend/js/login_page_form_validation.js') }}" type="text/javascript"></script>
    <!--end::Base Scripts -->
</body>
<!-- end::Body -->

</html>
