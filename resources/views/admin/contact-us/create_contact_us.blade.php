@extends('admin.layouts.default')
@section('inc_css')

@section('content')
@php
if(isset($contact_us)){
$title=trans('formname.update_contact_form');
}
else{
$title=trans('formname.create_contact_form');
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
                                <a href="{{route('contact_us_index')}}"
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
                        @if(isset($contact_us) || !empty($contact_us))
                        {{ Form::model($contact_us, ['route' => ['contact_us_store', $contact_us->id], 'method' => 'PUT','id'=>'m_form_1','class'=>'m-form m-form--fit m-form--label-align-right']) }}
                        @else
                        {{ Form::open(array('route' => 'contact_us_store','method'=>'POST','class'=>'m-form m-form--fit m-form--label-align-right','id'=>'m_form_1')) }}
                        @endif
                        <div class="m-portlet__body">
                            <div class="m-form__content">
                                <div class="m-alert m-alert--icon alert alert-danger m--hide" role="alert"
                                    id="m_form_1_msg">
                                    <div class="m-alert__icon">
                                        <i class="la la-warning"></i>
                                    </div>
                                    <div class="m-alert__text">
                                        Please fill all the required field & try again.
                                    </div>
                                    <div class="m-alert__close">
                                        <button type="button" class="close" data-close="alert" aria-label="Close">
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                {!! Form::label(trans('formname.full_name').'*', null,array('class'=>'col-form-label
                                col-lg-3
                                col-sm-12')) !!}
                                <div class="col-lg-6 col-md-9 col-sm-12">
                                    {!!
                                    Form::text('full_name',@$contact_us->full_name,array('class'=>'form-control
                                    m-input')) !!}
                                    @if ($errors->has('full_name')) <p style="color:red;">
                                        {{ $errors->first('full_name') }}</p> @endif
                                </div>
                            </div>

                            <div class="form-group m-form__group row">
                                {!! Form::label(trans('formname.email').'*', null,array('class'=>'col-form-label
                                col-lg-3
                                col-sm-12')) !!}
                                <div class="col-lg-6 col-md-9 col-sm-12">
                                    {!!
                                    Form::email('email',@$contact_us->email,array('class'=>'form-control
                                    m-input')) !!}
                                    @if ($errors->has('email')) <p style="color:red;">
                                        {{ $errors->first('email') }}</p> @endif
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                {!! Form::label(trans('formname.subject').'*', null,array('class'=>'col-form-label
                                col-lg-3
                                col-sm-12')) !!}
                                <div class="col-lg-6 col-md-9 col-sm-12">
                                    {!!
                                    Form::text('subject',@$contact_us->subject,array('class'=>'form-control
                                    m-input')) !!}
                                    @if ($errors->has('subject')) <p style="color:red;">
                                        {{ $errors->first('subject') }}</p> @endif
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                {!! Form::label(trans('formname.phone').'*', null,array('class'=>'col-form-label
                                col-lg-3
                                col-sm-12')) !!}
                                <div class="col-lg-6 col-md-9 col-sm-12">
                                    {!!
                                    Form::text('phone',@$contact_us->phone,array('class'=>'form-control
                                    m-input','id'=>'phone')) !!}
                                    @if ($errors->has('phone')) <p style="color:red;">
                                        {{ $errors->first('phone') }}</p> @endif
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                {!! Form::label(trans('formname.message').'*',
                                null,array('class'=>'col-form-label
                                col-lg-3
                                col-sm-12')) !!}
                                <div class="col-lg-6 col-md-9 col-sm-12">
                                    {!!
                                    Form::textarea('message',@$contact_us->message,array('class'=>'form-control
                                    m-input')) !!}
                                    @if ($errors->has('message')) <p style="color:red;">
                                        {{ $errors->first('message') }}</p> @endif
                                </div>
                            </div>
                            {!! Form::hidden('id',@$contact_us->id ,['id'=>'id']) !!}
                            <div class="m-portlet__foot m-portlet__foot--fit">
                                <div class="m-form__actions m-form__actions">
                                    <br>
                                    <div class="row">
                                        <div class="col-lg-9 ml-lg-auto">
                                            {!! Form::submit(trans('formname.submit'), ['class' => 'btn btn-success'] )
                                            !!}
                                            <a href="{{Route('contact_us_index')}}"
                                                class="btn btn-secondary">{{trans('formname.cancel')}}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@section('inc_script')
<script src="{{ asset('backend/js/contact-us/create.js') }}" type="text/javascript"></script>
@stop