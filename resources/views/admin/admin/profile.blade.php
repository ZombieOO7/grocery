@extends('admin.layouts.default')
@section('inc_css')
@section('content')
@php
$title=__('formname.profile');
@endphp

@section('title', $title)

<div class="m-grid__item m-grid__item--fluid m-wrapper">
    <div class="m-content">
        <div class="row">
            <div class="col-lg-12">
                <!--begin::Portlet-->
                @include('admin.includes.flashMessages')
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
                            {{-- <div class="m-portlet__head-tools">
                                <a href="{{route('profile')}}"
                                    class="btn btn-secondary m-btn m-btn--air m-btn--custom">
                                    <span>
                                        <i class="la la-arrow-left"></i>
                                        <span>Back</span>
                                    </span>
                                </a>
                            </div> --}}
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        @if(isset($admin) || !empty($admin))
                        {{ Form::model($admin, ['route' => ['profile_update', $admin->id], 'method' => 'PUT','id'=>'m_form_1','class'=>'m-form m-form--fit m-form--label-align-right','autocomplete' => 'off']) }}
                        @else
                        {{ Form::open(array('route' => 'profile_update','method'=>'post','class'=>'m-form m-form--fit m-form--label-align-right','id'=>'m_form_1')) }}
                        @endif
                        <div class="m-portlet__body">
                            <div class="m-form__content">
                                {{-- <div class="m-alert m-alert--icon alert alert-danger m--hide" role="alert"
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
                                </div> --}}
                            </div>
                            <div class="form-group m-form__group row">
                                {!! Form::label(__('formname.first_name').'*', null,array('class'=>'col-form-label
                                col-lg-3
                                col-sm-12')) !!}
                                <div class="col-lg-6 col-md-9 col-sm-12">
                                    {!!
                                    Form::text('first_name',@$admin->first_name,array('class'=>'form-control
                                    m-input', 'maxlength'=>config('constant.name_length'),'placeholder'=>__('formname.first_name'))) !!}
                                    @if ($errors->has('first_name')) <p style="color:red;">
                                        {{ $errors->first('first_name') }}</p> @endif
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                {!! Form::label(__('formname.last_name') .'*', null,array('class'=>'col-form-label
                                col-lg-3 col-sm-12'))
                                !!}
                                <div class="col-lg-6 col-md-9 col-sm-12">
                                    {!!
                                    Form::text('last_name',@$admin->last_name,array('class'=>'form-control
                                    m-input', 'maxlength'=>config('constant.name_length'),'placeholder'=>__('formname.last_name'))) !!}
                                    @if ($errors->has('last_name')) <p style="color:red;">
                                        {{ $errors->first('last_name') }}</p> @endif
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                {!! Form::label(__('formname.email'). '*', null,array('class'=>'col-form-label
                                col-lg-3
                                col-sm-12')) !!}
                                <div class="col-lg-6 col-md-9 col-sm-12">
                                    {!!
                                    Form::email('email',@$admin->email,array('class'=>'form-control
                                    m-input', 'maxlength'=>config('constant.email_length'),'placeholder'=>__('formname.email'))) !!}
                                    <span>
                                        @if ($errors->has('email')) <p style="color:red;">
                                            {{ $errors->first('email') }}</p> @endif
                                    </span>
                                </div>
                            </div>
                            {!! Form::hidden('id',@$admin->id ,['id'=>'id']) !!}
                            <div class="m-portlet__foot m-portlet__foot--fit">
                                <div class="m-form__actions m-form__actions">
                                    <br>
                                    <div class="row">
                                        <div class="col-lg-9 ml-lg-auto">
                                            {!! Form::submit(__('formname.submit'), ['class' => 'btn btn-success'] )
                                            !!}
                                            <a href="{{ route('admin_dashboard')}}"
                                                class="btn btn-secondary">{{__('formname.cancel')}}</a>
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
<script>
    var rule = $.extend({}, {!!json_encode(config('constant'), JSON_FORCE_OBJECT) !!});
</script>
<script src="{{ asset('backend/js/admin/create.js') }}" type="text/javascript"></script>
@stop