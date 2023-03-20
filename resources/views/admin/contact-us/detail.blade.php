@extends('admin.layouts.default')
@section('inc_css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.3/css/bootstrap-colorpicker.min.css"
    rel="stylesheet">
<style>
    label {
        font-weight: 600;
    }
    .hid_spn{
        display: none !important;
    }
</style>
@endsection
@section('content')
@php
if(isset($contact)){
$title=__('formname.contact_us_detail');
}
else{
$title=__('formname.contact_us_detail');
}
if(URL::previous() == Request::fullUrl()){
    $backurl = route('contact_us_index');
}else{
    $backurl = URL::previous();
}
@endphp

@section('title', $title)

<div class="m-grid__item m-grid__item--fluid m-wrapper">
    <div class="m-content">
        @include('admin.includes.flashMessages')
        <div class="row">
            <div class="col-lg-12">
                <!--begin::Portlet-->
                <div class="m-portlet m-portlet--last m-portlet--head-lg m-portlet--responsive-mobile"
                    id="main_portlet">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-wrapper">
                            <div class="m-portlet__head-caption">
                                <div class="m-portlet__head-title">
                                    <h3 class="m-portlet__head-text">
                                        {{$title}}
                                    </h3>
                                </div>
                            </div>
                            <div class="m-portlet__head-tools">
                                <a href="{{$backurl}}"
                                    class="btn btn-secondary m-btn m-btn--air m-btn--custom">
                                    <span>
                                        <i class="la la-arrow-left"></i>
                                        <span>Back</span>
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        <div class="form-group m-form__group row">
                            {!! Form::label(__('formname.full_name').' : ', null,['class'=>'col-form-label
                            col-lg-3
                            col-sm-12']) !!}
                            <div class="col-lg-6 col-md-9 col-sm-12 col-form-label">
                                {{@$contact->user->full_name_text}}
                            </div>
                        </div>
                        <div class="form-group m-form__group row aaaaa">
                            {!! Form::label(__('formname.email').' : ',
                            null,['class'=>'col-form-label
                            col-lg-3 col-sm-12']) !!}
                            <div class="col-lg-6 col-md-9 col-sm-12 col-form-label">
                                {{ @$contact->user->email }}
                            </div>
                        </div>
                        <div class="form-group m-form__group row aaaaa">
                            {!! Form::label(__('formname.subject').' : ',
                            null,['class'=>'col-form-label
                            col-lg-3 col-sm-12']) !!}
                            <div class="col-lg-6 col-md-9 col-sm-12 col-form-label">
                                {{ @$contact->subject }}
                            </div>
                        </div>
                        <div class="form-group m-form__group row">
                            {!! Form::label(__('formname.message').' : ', null,['class'=>'col-form-label
                            col-lg-3
                            col-sm-12']) !!}
                            <div class="col-lg-6 col-md-9 col-sm-12 col-form-label">
                                {{@$contact->message}}
                            </div>
                        </div>
                        <div class="form-group m-form__group row aaaaa">
                            {!! Form::label(__('formname.message_date').' : ',
                            null,['class'=>'col-form-label
                            col-lg-3 col-sm-12']) !!}
                            <div class="col-lg-6 col-md-9 col-sm-12 col-form-label">
                                {!! @$contact->proper_created_at !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@section('inc_script')
<script src="{{ asset('backend/js/job/create.js') }}" type="text/javascript"></script>
@endsection