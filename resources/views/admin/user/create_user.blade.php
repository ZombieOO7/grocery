@extends('admin.layouts.default')
@section('inc_css')

@section('content')
@php
if(isset($user)){
$title=__('formname.user_update');
}
else{
$title=__('formname.user_create');
}
@endphp

@section('title', $title)
<style>
    select {
        width: 100% !important;
        overflow: hidden !important;
        white-space: pre !important;
        text-overflow: ellipsis !important;
    }
</style>

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
                                <a href="{{route('user_index')}}"
                                    class="btn btn-secondary m-btn m-btn--air m-btn--custom">
                                    <span>
                                        <i class="la la-arrow-left"></i>
                                        <span>{{__('formname.back')}}</span>
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        @if(isset($user) || !empty($user))
                        {{ Form::model($user, ['route' => ['user_store', $user->id], 'method' => 'PUT','id'=>'m_form_1','class'=>'m-form m-form--fit m-form--label-align-right','enctype'=>'multipart/form-data']) }}
                        @else
                        {{ Form::open(['route' => 'user_store','method'=>'POST','class'=>'m-form m-form--fit m-form--label-align-right','id'=>'m_form_1','enctype'=>'multipart/form-data']) }}
                        @endif
                        <div class="m-portlet__body">
                            <div class="m-form__content">
                                <div class="m-alert m-alert--icon alert alert-danger m--hide" role="alert"
                                    id="m_form_1_msg">
                                    <div class="m-alert__icon">
                                        <i class="la la-warning"></i>
                                    </div>
                                    <div class="m-alert__text">
                                        {{__('admin/messages.required_alert')}}
                                    </div>
                                    <div class="m-alert__close">
                                        <button type="button" class="close" data-close="alert" aria-label="Close">
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                {!! Form::label(__('formname.first_name').'*', null,['class'=>'col-form-label
                                col-lg-3
                                col-sm-12']) !!}
                                <div class="col-lg-6 col-md-9 col-sm-12">
                                    {!!
                                    Form::text('first_name',isset($user)?$user->first_name:'',['class'=>'form-control
                                    m-input','maxlength'=>config('constant.user_name_length'),'placeholder'=>__('formname.first_name')])
                                    !!}
                                    @if ($errors->has('first_name')) <p style="color:red;">
                                        {{ $errors->first('first_name') }}</p> @endif
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                {!! Form::label(__('formname.last_name') .'*', null,['class'=>'col-form-label
                                col-lg-3 col-sm-12'])
                                !!}
                                <div class="col-lg-6 col-md-9 col-sm-12">
                                    {!!
                                    Form::text('last_name',isset($user)?$user->last_name:'',['class'=>'form-control
                                    m-input','maxlength'=>config('constant.user_name_length'),'placeholder'=>__('formname.last_name')])
                                    !!}
                                    @if ($errors->has('last_name')) <p style="color:red;">
                                        {{ $errors->first('last_name') }}</p> @endif
                                </div>
                            </div>
                            @if(!isset($user->id))
                            <div class="form-group m-form__group row">
                                {!! Form::label(__('formname.email'). '*', null,['class'=>'col-form-label col-lg-3 col-sm-12']) !!}
                                <div class="col-lg-6 col-md-9 col-sm-12">
                                    {!! Form::email('email',isset($user)?$user->email:'',['class'=>'form-control
                                    m-input','maxlength'=>config('constant.email_length'),'placeholder'=>__('formname.email')])
                                    !!}
                                    <span>
                                        @if ($errors->has('email')) <p style="color:red;">
                                            {{ $errors->first('email') }}</p> @endif
                                    </span>
                                </div>
                            </div>
                            @else
                                <div class="form-group m-form__group row">
                                    {!! Form::label(__('formname.email').'', null,['class'=>'col-form-label col-lg-3
                                    col-sm-12']) !!}
                                    <div class="col-lg-6 col-md-9 col-sm-12 pt-3">
                                        <b>{{@$user->email}}</b>
                                    </div>
                                </div>
                            @endif
                            <div class="form-group m-form__group row">
                                {!! Form::label(__('formname.phone') .'*', null,['class'=>'col-form-label
                                col-lg-3 col-sm-12'])
                                !!}
                                <div class="col-lg-6 col-md-9 col-sm-12">
                                    {!!
                                    Form::text('phone',isset($user)?$user->phone:'',['class'=>'form-control
                                    m-input','maxlength'=>config('constant.phone_length'),'placeholder'=>__('formname.phone')])
                                    !!}
                                    @if ($errors->has('phone')) <p style="color:red;">
                                        {{ $errors->first('phone') }}</p> @endif
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                {!! Form::label(__('formname.profile_picture') .'*', null,['class'=>'col-form-label
                                col-lg-3 col-sm-12'])
                                !!}
                                <div class="col-lg-6 col-md-9 col-sm-12">
                                    {!!Form::file('image',['id'=>'imgInput','class'=>'form-control m-input','accept' => 'image/*'])!!}
                                    <input type="hidden" name="stored_img_name" id="stored_img_id" value="{{@$user->profile_pic}}">
                                    @if ($errors->has('image')) <p style="color:red;">
                                        {{ $errors->first('image') }}</p>
                                    @endif
                                    @if($user)
                                        <img id="blah" src="{{@$user->profile_image }}" alt="" height="200px;" width="200px;"
                                        style="display:block;" />
                                    @else
                                    <img id="blah" src="" alt="" height="200px;" width="200px;"
                                        style="display:none;" />
                                    @endif
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                {!! Form::label(__('formname.company.title').'*', null,['class'=>'col-form-label col-lg-3
                                col-sm-12']) !!}
                                <div class="col-lg-6 col-md-9 col-sm-12">
                                    {!! Form::select('company_id', @$companyList,@$user->company_id,['id'=>'company_id','class' =>'form-control']) !!}
                                    @if ($errors->has('company_id')) <p style="color:red;">
                                        {{ $errors->first('company_id') }}</p> @endif
                                </div>
                            </div>
                            @if(!isset($user->id))
                            <div class="form-group m-form__group row">
                                {!! Form::label(__('formname.company_position').'*', null,['class'=>'col-form-label col-lg-3
                                col-sm-12']) !!}
                                <div class="col-lg-6 col-md-9 col-sm-12">
                                    {!! Form::select('user_type',@$userTypeList,@$user->user_type,['class' => 'form-control', 'id' => 'positionId']) !!}
                                    @if ($errors->has('user_type')) <p style="color:red;">
                                        {{ $errors->first('user_type') }}</p> @endif
                                </div>
                            </div>
                            @else
                            <div class="form-group m-form__group row">
                                {!! Form::label(__('formname.company_position').'', null,['class'=>'col-form-label col-lg-3
                                col-sm-12']) !!}
                                <div class="col-lg-6 col-md-9 col-sm-12 pt-3">
                                    <b>{{@config('constant.user_types')[$user->user_type]}}</b>
                                </div>
                            </div>
                            @endif
                            <div class="form-group m-form__group row">
                                {!! Form::label(__('formname.role_id').'*', null,['class'=>'col-form-label col-lg-3
                                col-sm-12']) !!}
                                <div class="col-lg-6 col-md-9 col-sm-12">
                                    {!! Form::select('role_id',@$subRoleList??[''=>'Select Role'],@$user->role_id,['class' => 'form-control', 'id'=>'roleId']) !!}
                                    @if ($errors->has('role_id')) <p style="color:red;">
                                        {{ $errors->first('role_id') }}</p> @endif
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                {!! Form::label(__('formname.password').'*', null,['class'=>'col-form-label col-lg-3 col-sm-12'])
                                !!}
                                <div class="col-lg-6 col-md-9 col-sm-12">
                                    <div class="input-group">
                                        {!! Form::password('password',['class' =>
                                        'form-control m-input','id'=>'password','type'
                                        =>'password','placeholder'=>__('formname.password'),
                                        'maxlength'=>config('constant.password_max_length'),'minlength'=>config('constant.password_min_length')]) !!}
                                    </div>
                                    <span class="passwordError">
                                        @if($errors->has('password'))
                                        <p class="errors">{{$errors->first('password')}}</p>
                                        @endif
                                    </span>
                                    <span class="m-form__help"></span>
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                {!! Form::label(__('formname.confirm_password').'*', null,['class'=>'col-form-label col-lg-3 col-sm-12'])
                                !!}
                                <div class="col-lg-6 col-md-9 col-sm-12">
                                    <div class="input-group">
                                        {!! Form::password('confirm_password',['class' =>
                                        'form-control m-input','id'=>'password','type'
                                        =>'confirm_password','placeholder'=>__('formname.confirm_password'),
                                        'maxlength'=>config('constant.password_max_length'),'minlength'=>config('constant.password_min_length')]) !!}
                                    </div>
                                    <span class="conformPasswordError">
                                        @if($errors->has('confirm_password'))
                                        <p class="errors">{{$errors->first('confirm_password')}}</p>
                                        @endif
                                    </span>
                                    <span class="m-form__help"></span>
                                </div>
                            </div>
                            @if(isset($job) && count($job) <= 0)
                            <div class="form-group m-form__group row">
                                {!! Form::label(__('formname.status').'*', null,['class'=>'col-form-label col-lg-3
                                col-sm-12']) !!}
                                <div class="col-lg-6 col-md-9 col-sm-12">
                                    {!! Form::select('status', @$statusList, @$userststus,
                                    ['class' =>
                                    'form-control' ]) !!}
                                </div>
                            </div>
                            @endif
                            {!! Form::hidden('id',isset($user)?$user->id:'' ,['id'=>'id']) !!}
                            <div class="m-portlet__foot m-portlet__foot--fit">
                                <div class="m-form__actions m-form__actions">
                                    <br>
                                    <div class="row">
                                        <div class="col-lg-9 ml-lg-auto">
                                            {!! Form::submit(__('formname.submit'), ['class' => 'btn btn-success'] )
                                            !!}
                                            <a href="{{Route('user_index')}}"
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
    var formname = $.extend({}, {!!json_encode(__('formname'), JSON_FORCE_OBJECT) !!});
    var id = '{{@$user->id}}';
    var getRoleUrl = "{{route('get-user-role')}}";
</script>
<script src="{{ asset('backend/js/user/create.js') }}" type="text/javascript"></script>
@stop