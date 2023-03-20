@extends('admin.layouts.default')
@section('inc_css')

@section('content')
@php
if(isset($cms)){
$title=__('formname.cms_update');
}
else{
$title=__('formname.cms_create');
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
                                <a href="{{route('cms_index')}}"
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
                        @if(isset($cms) || !empty($cms))
                        {{ Form::model($cms, ['route' => ['cms_store', $cms->id], 'method' => 'PUT','id'=>'m_form_1','class'=>'m-form m-form--fit m-form--label-align-right']) }}
                        @else
                        {{ Form::open(['route' => 'cms_store','method'=>'POST','class'=>'m-form m-form--fit m-form--label-align-right','id'=>'m_form_1']) }}
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
                                {!! Form::label(__('formname.title').'*', null,['class'=>'col-form-label
                                col-lg-3
                                col-sm-12']) !!}
                                <div class="col-lg-6 col-md-9 col-sm-12">
                                    {!!
                                    Form::text('page_title',@$cms->page_title,['class'=>'form-control
                                    m-input','maxlength'=>config('constant.name_length'),'placeholder'=>__('formname.title')]) !!}
                                    @if ($errors->has('page_title')) <p style="color:red;">
                                        {{ $errors->first('page_title') }}</p> @endif
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                {!! Form::label(__('formname.content').'*', null,['class'=>'col-form-label
                                col-lg-3
                                col-sm-12']) !!}
                                <div class="col-lg-6 col-md-9 col-sm-12">
                                    <div class="input-group">
                                        {!!
                                        Form::textarea('page_content',@$cms->page_content,['class'=>'form-control
                                        m-input','id'=>'editor1']) !!}
                                    </div>
                                    <span class="contentError">
                                        @if ($errors->has('page_content')) <p style="color:red;">
                                            {{ $errors->first('page_content') }}</p> @endif
                                    </span>
                                    <span class="m-form__help"></span>
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                {!! Form::label(__('formname.meta_keyword'), null,['class'=>'col-form-label
                                col-lg-3
                                col-sm-12']) !!}
                                <div class="col-lg-6 col-md-9 col-sm-12">
                                    {!!
                                    Form::text('meta_keyword',@$cms->meta_keyword,['class'=>'form-control
                                    m-input', 'maxlength'=>config('constant.name_length'),'placeholder'=>__('formname.meta_keyword')]) !!}
                                    @if ($errors->has('meta_keyword')) <p style="color:red;">
                                        {{ $errors->first('meta_keyword') }}</p> @endif
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                {!! Form::label(__('formname.meta_description'),
                                null,['class'=>'col-form-label col-lg-3 col-sm-12']) !!}
                                <div class="col-lg-6 col-md-9 col-sm-12">
                                    {!!
                                    Form::textarea('meta_description',@$cms->meta_description,['class'=>'form-control
                                    m-input', 'maxlength'=>config('constant.content_length'),'placeholder'=>__('formname.meta_description')]) !!}
                                    @if ($errors->has('meta_description')) <p style="color:red;">
                                        {{ $errors->first('meta_description') }}</p> @endif
                                </div>
                            </div>
                            @if(isset($cms))
                            @php $cmsststus = $cms->status;@endphp
                            @else
                            @php $cmsststus = null;@endphp
                            @endif
                            {{-- <div class="form-group m-form__group row">
                                {!! Form::label(__('formname.status').'*', null,['class'=>'col-form-label col-lg-3
                                col-sm-12']) !!}
                                <div class="col-lg-6 col-md-9 col-sm-12">
                                    {!! Form::select('status', @$statusList, $cmsststus,
                                    ['class' =>
                                    'form-control' ]) !!}
                                </div>
                            </div> --}}
                            {!! Form::hidden('id',@$cms->id ,['id'=>'id']) !!}
                            <div class="m-portlet__foot m-portlet__foot--fit">
                                <div class="m-form__actions m-form__actions">
                                    <br>
                                    <div class="row">
                                        <div class="col-lg-9 ml-lg-auto">
                                            {!! Form::submit(__('formname.submit'), ['class' => 'btn btn-success'] )
                                            !!}
                                            <a href="{{Route('cms_index')}}"
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
<script src="https://cdn.ckeditor.com/4.10.1/standard/ckeditor.js"></script>
<script src="{{ asset('backend/js/cms/create.js') }}" type="text/javascript"></script>
<script>
    var rule = $.extend({}, {!!json_encode(config('constant'), JSON_FORCE_OBJECT) !!});
console.log(rule);
//deal with copying the ckeditor text into the actual textarea
CKEDITOR.on('instanceReady', function() {
    $.each(CKEDITOR.instances, function(instance) {
        CKEDITOR.instances[instance].document.on("keyup", CK_jQ);
        CKEDITOR.instances[instance].document.on("paste", CK_jQ);
        CKEDITOR.instances[instance].document.on("keypress", CK_jQ);
        CKEDITOR.instances[instance].document.on("blur", CK_jQ);
        CKEDITOR.instances[instance].document.on("change", CK_jQ);
    });
});

function CK_jQ() {
    for (instance in CKEDITOR.instances) {
        CKEDITOR.instances[instance].updateElement();
    }
}

CKEDITOR.replace('editor1');
</script>
@stop