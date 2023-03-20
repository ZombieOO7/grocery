@extends('admin.layouts.default')
@section('content')
<style>
    .hid_spn{
        display: none !important;
    }
</style>
@section('title', __('formname.cms_list'))
<div class="m-grid__item m-grid__item--fluid m-wrapper">
    <!-- END: Subheader -->
    <div class="m-content">
        @include('admin.includes.flashMessages')
        <div class="m-portlet m-portlet--mobile">

            <div class="m-portlet__body">
                <div class="m-form__content">
                    <h5>{{__('formname.cms_list')}}</h5>
                </div>
                <hr>
                <div class="m-portlet__head" style="padding: 0 !important;">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            {{-- <select class="form-control" name="action" id='action' aria-invalid="false">
                                <option value="">{{__('formname.action_option')}}</option>
                                @if((\Auth::guard('admin')->user()->can('page multiple delete')))
                                <option value="{{config('constant.delete')}}">{{__('formname.delete')}}</option>
                                <option value="{{config('constant.active')}}">{{__('formname.active')}}</option>
                                <option value="{{config('constant.inactive')}}">{{__('formname.inactive')}}</option>
                                @endif
                            </select> --}}
                            {{-- <a href="javascript:;" class="btn btn-primary submit_btn"id='action_submit' data-url="{{route('cms_multi_delete')}}" data-table_name="cms_table">{{__('formname.submit')}}</a> --}}
                                  <button class="btn btn-info" style='' id='clr_filter'
                                data-table_name="cms_table">{{__('formname.clear_filter')}}</button>
                        </div>
                    </div>
                    {{-- <div class="m-portlet__head-tools">
                        <ul class="m-portlet__nav">
                            @if((\Auth::guard('admin')->user()->can('page create')))
                            <li class="m-portlet__nav-item">
                                <a href="{{Route('cms_create')}}"
                                    class="btn btn-accent m-btn m-btn--pill m-btn--custom m-btn--icon m-btn--air">
                                    <span>
                                        <i class="la la-plus"></i>
                                        <span>{{__('formname.new_record')}}</span>
                                    </span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div> --}}
                </div>
                <!--begin: Datatable -->
                <table class="table table-striped- table-bordered table-hover table-checkable" id="cms_table">
                    <thead>
                        <tr>
                            {{-- <th class="nosort">
                                <label class="m-checkbox m-checkbox--single m-checkbox--solid m-checkbox--brand">
                                    <input type="checkbox" value="" id="cms_checkbox" class="m-checkable allCheckbox">
                                    <span></span>
                                </label>
                            </th> --}}
                            {{-- <th>{{__('formname.id')}}</th> --}}
                            <th>{{__('formname.title')}}</th>
                            <th>{{__('formname.created_at')}}</th>
                            {{-- <th>{{__('formname.status')}}</th> --}}
                            <th>{{__('formname.action')}}</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            {{-- <th></th> --}}
                            {{-- <th><input type="text" class="form-control form-control-sm tbl-filter-column"
                                    placeholder="{{__('formname.id')}}"></th> --}}
                            <th><input type="text" class="form-control form-control-sm tbl-filter-column"
                                    placeholder="{{__('formname.title')}}"></th>
                            <th><input type="text" class="form-control form-control-sm tbl-filter-column"
                                    placeholder="{{__('formname.created_at')}}"></th>
                          {{-- <th>
                                <select class="statusFilter form-control form-control-sm tbl-filter-column">
                                    @forelse ($statusList as $key => $item)
                                    <option value="{{$key}}">{{$item}}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </th> --}}
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <!-- END EXAMPLE TABLE PORTLET-->
    </div>
</div>

@stop

<?php /*Load script to footer section*/?>

@section('inc_script')
<script>
var cms_list_url = "{{ route('cms_datatable') }}";
</script>
<script src="{{ asset('backend/js/cms/index.js') }}" type="text/javascript"></script>
@stop