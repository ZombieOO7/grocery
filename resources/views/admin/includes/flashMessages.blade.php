@if ($message = Session::get('message'))
<div class="m-alert m-alert--icon alert alert-success" role="alert" id="m_form_1_msg">
    <div class="m-alert__icon">
        <i class="la la-check"></i>
    </div>
    <div class="m-alert__text">
        {{ $message }}
    </div>
    <div class="m-alert__close">
        <button type="button" class="close" data-close="alert" aria-label="Close">
        </button>
    </div>
</div>
@endif

@if ($message = Session::get('success'))
<div class="m-alert m-alert--icon alert alert-success" role="alert" id="m_form_1_msg">
    <div class="m-alert__icon">
        <i class="la la-check"></i>
    </div>
    <div class="m-alert__text">
        {{ $message }}
    </div>
    <div class="m-alert__close">
        <button type="button" class="close" data-close="alert" aria-label="Close">
        </button>
    </div>
</div>
@endif


@if ($message = Session::get('error'))
<div class="m-alert m-alert--icon alert alert-danger" role="alert" id="m_form_1_msg">
    <div class="m-alert__icon">
        <i class="la la-check"></i>
    </div>
    <div class="m-alert__text">
        {{ $message }}
    </div>
    <div class="m-alert__close">
        <button type="button" class="close" data-close="alert" aria-label="Close">
        </button>
    </div>
</div>
@endif


@if ($message = Session::get('warning'))
<div class="m-alert m-alert--icon alert alert-warning" role="alert" id="m_form_1_msg">
    <div class="m-alert__icon">
        <i class="la la-check"></i>
    </div>
    <div class="m-alert__text">
        {{ $message }}
    </div>
    <div class="m-alert__close">
        <button type="button" class="close" data-close="alert" aria-label="Close">
        </button>
    </div>
</div>
@endif


@if ($message = Session::get('info'))
<div class="m-alert m-alert--icon alert alert-info" role="alert" id="m_form_1_msg">
    <div class="m-alert__icon">
        <i class="la la-check"></i>
    </div>
    <div class="m-alert__text">
        {{ $message }}
    </div>
    <div class="m-alert__close">
        <button type="button" class="close" data-close="alert" aria-label="Close">
        </button>
    </div>
</div>
@endif


@if ($errors->any())
    <div class="m-alert m-alert--icon alert alert-danger" role="alert" id="m_form_1_msg">
        <div class="m-alert__icon">
            {{-- <i class="la la-check"></i> --}}
        </div>
        <div class="m-alert__text">
            Please check the form below for errors
        </div>
        <div class="m-alert__close">
            <button type="button" class="close" data-close="alert" aria-label="Close">
            </button>
        </div>
    </div>
@endif