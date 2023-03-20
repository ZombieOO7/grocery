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
                                <a href="{{route('product.index')}}"
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
                        @if(isset($product) || !empty($product))
                        {{ Form::model($product, ['route' => ['product.store', @$product->uuid], 'method' => 'PUT','id'=>'m_form_1','class'=>'m-form m-form--fit m-form--label-align-right','files' => true,'autocomplete' => "off"]) }}
                        @else
                        {{ Form::open(['route' => 'product.store','method'=>'post','class'=>'m-form m-form--fit m-form--label-align-right','id'=>'m_form_1','files' => true,'autocomplete' => "off"]) }}
                        @endif
                            <div class="form-group m-form__group row">
                                {!! Form::label(__('formname.product.title').'*', null,['class'=>'col-form-label
                                col-lg-3
                                col-sm-12']) !!}
                                <div class="col-lg-6 col-md-9 col-sm-12">
                                    {!! Form::text('title',@$product->title,['id'=>'names','class'=>'form-control
                                    m-input err_msg','maxlength'=>config('constant.name_length'),'placeholder'=>__('formname.product.title')]) !!}
                                    @if ($errors->has('title'))
                                        <p style="color:red;">{{ $errors->first('title') }}</p> 
                                    @endif
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                {!! Form::label('category_id',__('formname.category.list')." <sup
                                    class='rqvr'>*</sup>" ,['class'=>'col-form-label col-lg-3
                                col-sm-12'],false) !!}
                                <div class="col-lg-6 col-md-9 col-sm-12">
                                    {!! Form::select('category_id', @$categoryList, @$product->category_id,
                                    ['id' => 'category_id','class' =>'form-control selectpicker','placeholder' => __('formname.select')])
                                    !!}
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                {!! Form::label('sub_category_id',__('formname.subcategory.list')." <sup
                                    class='rqvr'>*</sup>" ,['class'=>'col-form-label col-lg-3
                                col-sm-12'],false) !!}
                                <div class="col-lg-6 col-md-9 col-sm-12">
                                    {!! Form::select('sub_category_id', [], @$product->category_id,
                                    ['id' => 'sub_category_id','class' =>'form-control selectpicker','placeholder' => __('formname.select')])
                                    !!}
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                {!! Form::label(__('formname.image').'*', null,['class'=>'col-form-label
                                col-lg-3
                                col-sm-12']) !!}
                                <div class="col-lg-6 col-md-9 col-sm-12 img_msg_scn">
                                    <div class="input-group err_msg">
                                        {!! Form::file('images[]', ['class'=>'custom-file-input' ,'id'=>'imgInp2',
                                        'accept' => 'image/*','multiple'=>true]) !!}
                                        {!! Form::label('Choose file', null,['class'=>'custom-file-label']) !!}
                                        <input type="hidden" name="stored_image" id="stored_img_id"
                                            value="{{@$product->image}}">
                                        </br>
                                        @if ($errors->has('image')) <p style="color:red;">
                                            {{ $errors->first('image') }}</p> @endif
                                    </div>
                                    @if ($errors->has('imgInp'))
                                    <p style="color:red;">
                                        {{ $errors->first('imgInphttps://docs.google.com/spreadsheets/d/1sVtcgp0Or_ujHYnwbMewkmecymD6UJfe/edit#gid=251971411') }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group m-form__group row" id="blah">
                                <div class="col-form-label col-lg-3 col-sm-12"></div>
                                <div class="col-lg-6 col-md-9 col-sm-12 row dynamicImages">
                                    @forelse(@$product->productMedia??[] as $media)
                                    <div class='col-md-6'>
                                        <img class="imgHeightWidth" src="{{$media->attachment->image_path}}" alt="" style="display:block;" />
                                    </div>
                                    @empty
                                    @endforelse
                                </div>
                            </div>
                            {{-- <div class="form-group m-form__group row">
                                {!! Form::label(__('formname.product.short_description').'*', null,['class'=>'col-form-label col-lg-3 col-sm-12']) !!}
                                <div class="col-lg-6 col-md-9 col-sm-12">
                                    <div class="input-group">
                                        {!! Form::textarea('short_description',@$product->short_description,['class'=>'form-control
                                        m-input do-not-ignore']) !!}
                                    </div>
                                    <span class="shortDescriptionError">
                                        @if ($errors->has('short_description')) <p class='errors' style="color:red;">
                                            {{ $errors->first('short_description') }}</p>
                                        @endif
                                    </span>
                                    <span class="m-form__help"></span>
                                </div>
                            </div> --}}
                            <div class="form-group m-form__group row">
                                {!! Form::label(__('formname.product.price').'*', null,['class'=>'col-form-label
                                col-lg-3
                                col-sm-12']) !!}
                                <div class="col-lg-6 col-md-9 col-sm-12">
                                    {!! Form::text('price',@$product->price,['id'=>'names','class'=>'form-control
                                    m-input err_msg','maxlength'=>config('constant.name_length'),'placeholder'=>__('formname.product.price')]) !!}
                                    @if ($errors->has('price'))
                                        <p style="color:red;">{{ $errors->first('price') }}</p> 
                                    @endif
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                {!! Form::label(__('formname.product.description').'*', null,['class'=>'col-form-label col-lg-3 col-sm-12']) !!}
                                <div class="col-lg-6 col-md-9 col-sm-12">
                                    <div class="input-group">
                                        {!! Form::textarea('description',@$product->description,['class'=>'form-control
                                        m-input do-not-ignore','id'=>'editor1']) !!}
                                    </div>
                                    <span class="descriptionError">
                                        @if ($errors->has('description')) <p class='errors' style="color:red;">
                                            {{ $errors->first('description') }}</p>
                                        @endif
                                    </span>
                                    <span class="m-form__help"></span>
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                {!! Form::label(__('formname.product.stock_status').'*', null,['class'=>'col-form-label col-lg-3
                                col-sm-12']) !!}
                                <div class="col-lg-6 col-md-9 col-sm-12">
                                    {!! Form::select('stock_status', @$stockStatusList, @$product->stock_status,
                                    ['class' =>
                                    'form-control' ]) !!}
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                {!! Form::label(__('formname.status').'*', null,['class'=>'col-form-label col-lg-3
                                col-sm-12']) !!}
                                <div class="col-lg-6 col-md-9 col-sm-12">
                                    {!! Form::select('status', @$statusList, @$product->status,
                                    ['class' =>
                                    'form-control' ]) !!}
                                </div>
                            </div>
                            {!! Form::hidden('id',@$product->id ,['id'=>'id']) !!}
                            <div class="m-portlet__foot m-portlet__foot--fit">
                                <div class="m-form__actions m-form__actions">
                                    <br>
                                    <div class="row">
                                        <div class="col-lg-9 ml-lg-auto">
                                            {!! Form::submit(__('formname.submit'), ['class' => 'btn btn-success'] )
                                            !!}
                                            <a href="{{Route('product.index')}}"
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
<script src="https://cdn.ckeditor.com/4.10.1/standard/ckeditor.js"></script>
<script>
    $(document).find('#category_id').selectpicker({  
        placeholder: "Select Category",
        allowClear: true
    })
    $(document).find('#sub_category_id').selectpicker({  
        placeholder: "Select Sub Category",
        allowClear: true
    }) 
    // $(document).find("#paper_category").select2();
</script>
<script src="{{asset('js/jquery-ui.js')}}"></script>
<script>
var rule = $.extend({}, {!!json_encode(config('constant'), JSON_FORCE_OBJECT) !!});
var formname = $.extend({}, {!!json_encode(__('formname'), JSON_FORCE_OBJECT) !!});
var id = '{{@$user->id}}';
var url = "{{ route('product.datatable') }}";
var getSubCat = "{{route('get-sub-cat-list')}}";
</script>
<script src="{{ asset('backend/js/product/create.js') }}" type="text/javascript"></script>
@stop