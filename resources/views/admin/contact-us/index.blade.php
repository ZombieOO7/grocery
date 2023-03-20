@extends('admin.layouts.default')
@section('content')
<style>
    .hid_spn{
        display: none !important;
    }
</style>
@section('title', trans('formname.contact_us_list'))
<div class="m-grid__item m-grid__item--fluid m-wrapper">
    <!-- END: Subheader -->
    <div class="m-content">
        @include('admin.includes.flashMessages')
        <div class="m-portlet m-portlet--mobile">

            <div class="m-portlet__body">
                <div class="m-form__content">
                    <h5>{{trans('formname.contact_us_list')}}</h5>
                </div>
                <hr>
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <select class="form-control" name="action" id='action' aria-invalid="false">
                                <option value="">{{trans('formname.action')}}</option>
                                @if((\Auth::guard('admin')->user()->can('contact us multiple delete')))
                                <option value="delete">{{trans('formname.delete')}}</option>
                                @endif
                            </select>
                            <a href="javascript:;" class="btn btn-primary submit_btn" id='action_submit'
                                data-url="{{route('contact_us_multi_delete')}}"
                                data-table_name="contact_us_table">{{trans('formname.submit')}}</a>
                            <button class="btn btn-info" style='margin:0px 0px 0px 12px' id='clr_filter'
                                data-table_name="contact_us_table">{{trans('formname.clear_filter')}}</button>
                        </div>
                        {{-- <div class="row">
                            <div class="col-md-12 d_block_flx">
                                <button type="button" id="bulk_mail_btn"
                                    class="btn btn-success btn_wt_txt">{{ __('formname.bulk_mail') }}</button>
                                <!-- <p class="errors">{{ __('formname.bulk_mail_msg') }}</p> -->
                            </div>
                        </div> --}}

                    </div>

                    {{-- <div class="m-portlet__head-tools">
                        <ul class="m-portlet__nav">
                            @if((\Auth::guard('admin')->user()->can('contact us create')))
                            <li class="m-portlet__nav-item">
                                <a href="{{Route('contact_us_create')}}"
                                    class="btn btn-accent m-btn m-btn--pill m-btn--custom m-btn--icon m-btn--air">
                                    <span>
                                        <i class="la la-plus"></i>
                                        <span>{{trans('formname.new_record')}}</span>
                                    </span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div> --}}
                </div>
                {{-- <p class="errors">{{ __('formname.bulk_mail_msg') }}</p> --}}
                <!--begin: Datatable -->
                <table class="table table-striped- table-bordered table-hover table-checkable" id="contact_us_table">
                    <thead>
                        <tr>
                            <th class="nosort">
                                <label class="m-checkbox m-checkbox--single m-checkbox--solid m-checkbox--brand">
                                    <input type="checkbox" value="" id="contact_us_checkbox"
                                        class="m-checkable allCheckbox" onclick="CheckUncheckAll(this)">
                                    <span></span>
                                </label>
                            </th>
                            {{-- <th>{{trans('formname.id')}}</th> --}}
                            <th>{{trans('formname.full_name')}}</th>
                            <th>{{trans('formname.email')}}</th>
                            <th>{{trans('formname.subject')}}</th>
                            <th>{{trans('formname.message')}}</th>
                            <th>{{trans('formname.message_date')}}</th>
                            <th>{{trans('formname.action')}}</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <td></td>
                            {{-- <th><input type="text" class="form-control form-control-sm tbl-filter-column"
                                    placeholder="{{trans('formname.id')}}"></th> --}}
                            <th><input type="text" class="form-control form-control-sm tbl-filter-column"
                                    placeholder="{{trans('formname.full_name')}}"></th>
                            <th><input type="text" class="form-control form-control-sm tbl-filter-column"
                                    placeholder="{{trans('formname.email')}}"></th>
                            <th><input type="text" class="form-control form-control-sm tbl-filter-column"
                                    placeholder="{{trans('formname.subject')}}"></th>
                            <th><input type="text" class="form-control form-control-sm tbl-filter-column"
                                    placeholder="{{trans('formname.message')}}"></th>
                            <th><input type="text" class="form-control form-control-sm tbl-filter-column"
                                    placeholder="{{trans('formname.message_date')}}"></th>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <!-- END EXAMPLE TABLE PORTLET-->
    </div>
</div>
<!-- Show Description Modal -->
<div class="modal fade def_mod dtails_mdl" id="DescModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content ">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body">
                <h3 class="mdl_ttl">{{ __('formname.message') }}</h3>
                <p class="mrgn_tp_20 show_desc" style="word-wrap: break-word;">

                </p>
                <button type="button" class="btn btn-success pull-right" data-dismiss="modal">{{__('formname.close')}}</button>
            </div>
        </div>
    </div>
</div>

<!-- Show Description Modal -->
<div class="modal fade def_mod dtails_mdl" id="SubjModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content ">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body">
                <h3 class="mdl_ttl">{{ __('formname.subject') }}</h3>
                <p class="mrgn_tp_20 show_sbjct" style="word-wrap: break-word;">

                </p>
                <button type="button" class="btn btn-success pull-right" data-dismiss="modal">{{__('formname.close')}}</button>
            </div>
        </div>
    </div>
</div>
@stop

<?php /*Load script to footer section*/?>

@section('inc_script')
<script>
    var contact_us_list_url = "{{ route('contact_us_datatable') }}";
/** Show description modal */
$(document).on('click','.shw-dsc',function(e) {
    $(document).find('.show_desc').html($(this).attr('data-description'));
    $(document).find('.show_sbjct').html($(this).attr('data-subject'));
});
</script>
<script src="{{ asset('backend/js/contact-us/index.js') }}" type="text/javascript"></script>
@stop