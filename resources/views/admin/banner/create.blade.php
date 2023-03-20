@extends('admin.layouts.default')
@section('content')
@section('title', @$title)
<link rel="stylesheet" href="{{asset('css/jquery-ui.css')}}">
<style>
    .hid_spn{
        display: none !important;
    }
    .imgHeightWidth{
        width: 250px;
        height: 250px;
    }
</style>
<style>
    .commonSelect {
        width: 100% !important;
        overflow: hidden !important;
        white-space: pre !important;
        text-overflow: ellipsis !important;
    }
    .ui-autocomplete {
    max-height: 100px;
    overflow-y: auto;
    /* prevent horizontal scrollbar */
    overflow-x: hidden;
  }
  /* IE 6 doesn't support max-height
   * we use height instead, but this forces the menu to always be this tall
   */
  * html .ui-autocomplete {
    height: 100px;
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
                                        {{@$title}}
                                    </h3>
                                </div>
                            </div>
                            <div class="m-portlet__head-tools">
                                <a href="{{route('banner.index')}}"
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
                        @if(isset($banner) || !empty($banner))
                        {{ Form::model($banner, ['route' => ['banner.store', @$banner->uuid], 'method' => 'PUT','id'=>'m_form_1','class'=>'m-form m-form--fit m-form--label-align-right','files' => true,'autocomplete' => "off"]) }}
                        @else
                        {{ Form::open(['route' => 'banner.store','method'=>'post','class'=>'m-form m-form--fit m-form--label-align-right','id'=>'m_form_1','files' => true,'autocomplete' => "off"]) }}
                        @endif
                            <div class="form-group m-form__group row">
                                {!! Form::label(__('formname.image').'*', null,['class'=>'col-form-label
                                col-lg-3
                                col-sm-12']) !!}
                                <div class="col-lg-6 col-md-9 col-sm-12 img_msg_scn">
                                    <div class="input-group err_msg">
                                        {!! Form::file('image', ['class'=>'custom-file-input' ,'id'=>'imgInp',
                                        'accept' => 'image/*']) !!}
                                        {!! Form::label('Choose file', null,['class'=>'custom-file-label']) !!}
                                        <input type="hidden" name="stored_image" id="stored_img_id"
                                            value="{{@$banner->image}}">
                                        </br>
                                        @if ($errors->has('image')) <p style="color:red;">
                                            {{ $errors->first('image') }}</p> @endif
                                    </div>
                                    @if ($errors->has('imgInp'))
                                    <p style="color:red;">
                                        {{ $errors->first('imgInphttps://docs.google.com/spreadsheets/d/1sVtcgp0Or_ujHYnwbMewkmecymD6UJfe/edit#gid=251971411') }}
                                    </p>
                                    @endif
                                    <img id="blah" class="imgHeightWidth" src="{{@$banner->attachment->image_path }}" alt="" max-width= "100%";
                                    height= "auto";
                                        style="{{ isset($banner->image) ? 'display:block;' : 'display:none;' }}" />
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                {!! Form::label(__('formname.status').'*', null,['class'=>'col-form-label col-lg-3
                                col-sm-12']) !!}
                                <div class="col-lg-6 col-md-9 col-sm-12">
                                    {!! Form::select('status', @$statusList, @$banner->status,
                                    ['class' =>
                                    'form-control' ]) !!}
                                </div>
                            </div>
                            {!! Form::hidden('id',@$banner->id ,['id'=>'id']) !!}
                            <div class="m-portlet__foot m-portlet__foot--fit">
                                <div class="m-form__actions m-form__actions">
                                    <br>
                                    <div class="row">
                                        <div class="col-lg-9 ml-lg-auto">
                                            {!! Form::submit(__('formname.submit'), ['class' => 'btn btn-success'] )
                                            !!}
                                            <a href="{{Route('banner.index')}}"
                                                class="btn btn-secondary">{{__('formname.cancel')}}</a>
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
    $(document).find('.selectpicker').selectpicker({  
        placeholder: "Select Category",
        allowClear: true
    }) 
    // $(document).find("#paper_category").select2();
</script>
<script src="{{asset('js/jquery-ui.js')}}"></script>
<script>
var rule = $.extend({}, {!!json_encode(config('constant'), JSON_FORCE_OBJECT) !!});
var formname = $.extend({}, {!!json_encode(__('formname'), JSON_FORCE_OBJECT) !!});
var id = '{{@$user->id}}';
var url = "{{ route('banner.datatable') }}";
</script>
<script src="{{ asset('backend/js/banner/create.js') }}" type="text/javascript"></script>
@stop