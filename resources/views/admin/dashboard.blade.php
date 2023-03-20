@extends('admin.layouts.default')
@push('inc_css')
@endpush
@section('content')
<style>
    .hid_spn{
        display: none !important;
    }
</style>
@section('title', 'Dashboard')
<div class="m-grid__item m-grid__item--fluid m-wrapper adminDashboard">
    <!-- BEGIN: Subheader -->
    <div class="m-subheader ">
        <div class="d-flex align-items-center" >
            <div class="mr-auto">
                <h3 class="m-subheader__title ">Dashboard</h3>
            </div>
        </div>
    </div>
    <div class="m-content " style="padding-left:15px !important;" >
        <div class="col-md-12">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="dashboard-stat blue-madison">
                        <div class="visual">
                            <i class="flaticon-user"></i>
                        </div>
                        <div class="details">
                            <div class="number">
                                {{$totalUsers ?? 0}}
                            </div>
                            <div class="desc">
                                {{__('formname.dashboard_array.total_users')}} <i
                                    class="m-icon-swapright m-icon-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>
<!-- Show Description Modal -->
<div class="modal fade def_mod dtails_mdl" id="DescModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content ">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body">
                <h3 class="mdl_ttl"></h3>
                <p class="mrgn_tp_20 show_desc" style="word-wrap: break-word;">
                </p>
                <button type="button" class="btn btn-success pull-right" data-dismiss="modal">{{__('formname.close')}}</button>
            </div>
        </div>
    </div>
</div>
@stop
@section('inc_script')
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script src="{{asset('backend/js/dashboard/index.js')}}"></script>
<script>
    $(document).on('click','.shw-dsc',function(e) {
        $(document).find('.show_desc').html($(this).attr('data-description'));
        $(document).find('.mdl_ttl').html($(this).attr('data-title'));
    });
</script>
@stop