@extends('admin.layouts.default')
@section('content')
@php
$title = 'Job Report';
@endphp
@section('title', @$title)
@php
$months = monthList();
$date = dateList();
@endphp

<div class="m-grid__item m-grid__item--fluid m-wrapper">
	<div class="m-content">
		<div class="row">
			<div class="col-lg-12">
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
						</div>
					</div>
					<div class="m-portlet__body">
						<div class="m-form__section m-form__section--first">
							{{ Form::open(['route' => 'report.generate','method'=>'POST','class'=>'m-form m-form--fit m-form--label-align-right','id'=>'m_form_1']) }}
							<div class="m-portlet__body">
								<div class="m-form__content">
									@include('admin.includes.flashMessages')
									@if ($errors->any())
										<div class="alert alert-danger">
											<ul>
												@foreach ($errors->all() as $error)
													<li>{{ $error }}</li>
												@endforeach
											</ul>
										</div>
									@endif
									<div class="m-alert m-alert--icon alert alert-danger m--hide" role="alert"
										id="m_form_1_msg">
										<div class="m-alert__icon">
											<i class="la la-warning"></i>
										</div>
										<div class="m-alert__text">
											{{__('admin/messages.required_alert')}}.
										</div>
										<div class="m-alert__close">
											<button type="button" class="close" data-close="alert" aria-label="Close">
											</button>
										</div>
									</div>
									<div class="m-alert m-alert--icon alert alert-danger m--hide" role="alert"
										id="m_form_2_msg">
										<div class="m-alert__icon">
											<i class="la la-warning"></i>
										</div>
										<div class="m-alert__text">
											{{__('admin/messages.not_found')}}.
										</div>
										<div class="m-alert__close">
											<button type="button" class="close" data-close="alert" aria-label="Close">
											</button>
										</div>
									</div>
								</div>
								<div class="m-form__actions">
									<div class="row">
										<div class="col-lg-10 ml-lg-auto">
											{!! Form::button('Annually', ['id'=>'yearly','class' => 'btn btn-primary'] )
											!!}
											{!! Form::button('Monthly', ['id'=>'monthly','class' => 'btn btn-primary'] )
											!!}
											{!! Form::button('Daily', ['id'=>'daily','class' => 'btn btn-primary'] )
											!!}
											{!! Form::hidden('reportCategory',null,['id'=>'reportCategory']) !!}
										</div>
									</div>
								</div>
								<div class="form-group m-form__group row" id='yearInput' style="display:none;">
									{!! Form::label(__('formname.report.year').'*', null,['class'=>'col-form-label col-lg-2 col-sm-12']) !!}
									<div class="col-lg-6 col-md-9 col-sm-12">
										{!!
										Form::text('year',@$report->year,['class'=>'form-control
										m-input','id'=>'year','readonly'=>true]) !!}
										@if ($errors->has('year')) <p style="color:red;">
											{{ $errors->first('year') }}</p> @endif
									</div>
								</div>
								<div class="form-group m-form__group row" id='monthInput' style="display:none;">
									{!! Form::label(__('formname.report.month'),
									null,array('class'=>'col-form-label col-lg-2 col-sm-12'))!!}
									<div class="col-lg-6 col-md-9 col-sm-12">
										<div class="input-group">
											{!!Form::select('month',@$months,[],['class'=>'form-control
											m-bootstrap-select ','id'=>'month','multiple'=>false, 'style'=>'width:100%;','data-none-selected-text' => __('formname.report.select_month') ])!!}
										</div>
										<span class="monthsError">
											@if($errors->has('months'))
											<p class="errors">{{$errors->first('month')}}</p>
											@endif
										</span>
										<span class="m-form__help"></span>
									</div>
								</div>
								<div class="form-group m-form__group row" id='dayInput' style="display:none;">
									{!! Form::label(__('formname.report.date'),
									null,array('class'=>'col-form-label
									col-lg-2 col-sm-12'))
									!!}
									<div class="col-lg-6 col-md-9 col-sm-12">
										<div class="input-group">
											{!!Form::select('date',@$date,[],['class'=>'form-control
											m-bootstrap-select ','id'=>'date','style'=>'width:100%;','multiple'=>false ,'data-none-selected-text' => __('formname.report.select_date') ])!!}
										</div>
										<span class="daysError">
											@if($errors->has('days'))
											<p class="errors">{{$errors->first('days')}}</p>
											@endif
										</span>
										<span class="m-form__help"></span>
									</div>
								</div>
								<div class="form-group m-form__group row">
									{!! Form::label(__('formname.report.report_type').'*',
									null,array('class'=>'col-form-label
									col-lg-2 col-sm-12'))
									!!}
									<div class="col-lg-6 col-md-9 col-sm-12">
										<div class="input-group">
											{!!Form::select('report_type',@$reportType,[],['class'=>'form-control ','id'=>'reportType','multiple'=>false 
											,'data-none-selected-text' => __('formname.select_type',['type'=>'job Status']) ])!!}
										</div>
										<span class="reportTypeError">
											@if($errors->has('report_type'))
											<p class="errors">{{$errors->first('report_type')}}</p>
											@endif
										</span>
										<span class="m-form__help"></span>
									</div>
								</div>
								<div id="dynamicFilters">

								</div>
								{{-- <div class="form-group m-form__group row">
									{!! Form::label(__('formname.report.job_status'),
									null,array('class'=>'col-form-label
									col-lg-2 col-sm-12'))
									!!}
									<div class="col-lg-6 col-md-9 col-sm-12">
										<div class="input-group">
											{!!Form::select('status',@$jobStatusList,[],['class'=>'form-control
											m-bootstrap-select ','id'=>'status','multiple'=>false 
											,'data-none-selected-text' => __('formname.select_type',['type'=>'job Status']) ])!!}
										</div>
										<span class="daysError">
											@if($errors->has('days'))
											<p class="errors">{{$errors->first('days')}}</p>
											@endif
										</span>
										<span class="m-form__help"></span>
									</div>
								</div> --}}
								<div class="form-group m-form__group row">
									{!! Form::label(__('formname.report.export_to').'*',
									null,array('class'=>'col-form-label
									col-lg-2 col-sm-12'))
									!!}
									<div class="col-lg-6 col-md-9 col-sm-12">
										<div class="input-group">
											{!!Form::select('export_to',@[''=>'Select','.csv'=>'CSV','.xls'=>'Excel'],[],['class'=>'form-control','id'=>'export_to'
											,'data-none-selected-text' => __('formname.report.export_to') ])!!}
										</div>
										<span class="exportError">
											@if($errors->has('export_to'))
											<p class="errors">{{$errors->first('export_to')}}</p>
											@endif
										</span>
										<span class="m-form__help"></span>
									</div>
								</div>
								{!! Form::hidden('id',@$report->id ,['id'=>'id']) !!}
								<div class="m-portlet__foot m-portlet__foot--fit">
									<div class="m-form__actions m-form__actions">
										<div class="row">
											<div class="col-lg-10 ml-lg-auto">
												{!! Form::button(__('formname.view'), ['id'=>'viewReport','class' => 'btn btn-success'] )
												!!}
												{!! Form::submit(__('formname.export'), ['class' => 'btn btn-primary'] )
												!!}
												{!! Form::button(__('formname.clear_filter'), ['id'=>'clearBtn','class' => 'btn btn-info'] )
												!!}
											</div>
										</div>
									</div>
								</div>
							</div>
							{!! Form::close() !!}
							<div class="m-portlet__body" id="tableContent">
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
<script>
	var url = "{{route('report.view')}}";
	var getReportType = "{{route('report.type.data')}}";
	var getMachineList = "{{route('getmachine')}}";
</script>
<script src="{{ asset('backend/js/report/index.js') }}" type="text/javascript"></script>
@stop