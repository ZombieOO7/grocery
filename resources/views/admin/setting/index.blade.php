@extends('admin.layouts.default')
@section('inc_css')

@section('content')
@php
if(isset($permission)){
$title=__('formname.permission_update');
}
else{
$title=__('formname.web_setting.name');
}
$CONSTANT = config('constant')['websetting'];
$ACTIVETAB = $CONSTANT['active_tab'];
$activeTab = (old('active_tab')!= null)?old('active_tab'):Session::get('active_tab');
if ($activeTab != null){
}else{
$activeTab = $ACTIVETAB[1];
}
@endphp
@section('title', $title)

<div class="m-grid__item m-grid__item--fluid m-wrapper">
	<div class="m-content">
		@include('admin.includes.flashMessages')
		<div class="row">
			<div class="col-lg-12">
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
								<a href="{{route('web_setting_index')}}"
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
						<div class="m-wizard__form-step m-wizard__form-step--current" id="web_setting_form_step">
							<div class="row">
								<div class="col-xl-12">
									<ul class="nav nav-tabs m-tabs-line--2x m-tabs-line m-tabs-line--danger"
										role="tablist">
										<li class="nav-item m-tabs__item">
											<a class="nav-link m-tabs__link {{($activeTab==$ACTIVETAB[1])?'active':''}}"
												data-toggle="tab" href="#general_tab"
												role="tab">{{__('formname.web_setting.general')}}</a>
										</li>
									</ul>
									<div class="tab-content m--margin-top-40">
										<div class="tab-pane {{($activeTab==$ACTIVETAB[1])?'active':''}}"
											id="general_tab" role="tabpanel">
											<div class="m-form__section m-form__section--first">
												@if(isset($setting) || !empty($setting))
												{{ Form::model($setting, [ 'enctype'=>'multipart/form-data','route' => ['general_setting_store', $setting->id], 'method' => 'PUT','id'=>'web_general_setting_form','class'=>'m-form m-form--fit m-form--label-align-right']) }}
												@else
												{{ Form::open(['enctype'=>'multipart/form-data','route' => 'general_setting_store','method'=>'POST','class'=>'m-form m-form--fit m-form--label-align-right','id'=>'web_general_setting_form']) }}
												@endif
												<div class="m-portlet__body">
													<div class="m-form__content">
														<div class="m-alert m-alert--icon alert alert-danger m--hide"
															role="alert" id="web_general_form_msg">
															<div class="m-alert__icon">
																<i class="la la-warning"></i>
															</div>
															<div class="m-alert__text">
																{{__('formname.web_setting.error_msg')}}.
															</div>
															<div class="m-alert__close">
																<button type="button" class="close" data-close="alert"
																	aria-label="Close">
																</button>
															</div>
														</div>
													</div>
													<div class="form-group m-form__group row">
														{!! Form::label(__('formname.web_setting.send_email').'*', null,['class'=>'col-form-label
														col-lg-3
														col-sm-12']) !!}
														<div class="col-lg-3 col-md-9 col-sm-12">
															<a class="cd-stts" href="javascript:;" data-status="{{ @$setting->send_email==1 ? 1 : 0}}" title="{{ @$setting->send_email==1 ? 'Active' : 'Inactive' }}">
																<i class="{{ @$setting->send_email == 1 ? 'fas fa-toggle-on' : 'fas fa-toggle-off' }}" id="tggl-clss" style="font-size: 25px;"></i>
																<input type="hidden" value="{{ @$setting->send_email==1 ? 1 : 0}}" name="send_email" id="hidden_send_email"/>
															</a>
														</div>
													</div>
													<div class="form-group m-form__group row">
														{!! Form::label(__('formname.web_setting.notify').'*', null,['class'=>'col-form-label
														col-lg-3
														col-sm-12']) !!}
														<div class="col-lg-3 col-md-9 col-sm-12">
															<a class="cd-stts" href="javascript:;" data-status="{{ @$setting->notify==1 ? 1 : 0}}" title="{{ @$setting->notify==1 ? 'Active' : 'Inactive' }}">
																<i class="{{ @$setting->notify == 1 ? 'fas fa-toggle-on' : 'fas fa-toggle-off' }}" id="tggl-clss" style="font-size: 25px;"></i>
																<input type="hidden" value="{{ @$setting->notify==1 ? 1 : 0}}" name="notify" id="hidden_notify"/>
															</a>
														</div>
													</div>
													{!! Form::hidden('id',@$setting->id,['id'=>'id']) !!}
													{!! Form::hidden('active_tab',$ACTIVETAB[1],['id'=>'active_tab'])
													!!}
													<div class="m-portlet__foot m-portlet__foot--fit">
														<div class="m-form__actions m-form__actions">
															<br>
															<div class="row">
																<div class="col-lg-9 ml-lg-auto">
																	{!! Form::submit(__('formname.submit'), ['class' =>
																	'btn btn-success'] )
																	!!}
																	<a href="{{Route('admin_dashboard')}}"
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
	$(document).find('.cd-stts').on('click',function(){
		if($(this).attr('data-status') == 1) {
			$(this).find("i").removeClass('fas fa-toggle-on');
			$(this).find("i").addClass('fas fa-toggle-off');
			$(this).attr('data-status',0);
			$(this).find("input").val(0);
		} else {
			$(this).find("i").removeClass('fas fa-toggle-off');
			$(this).find("i").addClass('fas fa-toggle-on');
			$(this).attr('data-status',1);
			$(this).find("input").val(1);
		}
	});
</script>
<script src="{{ asset('backend/js/websetting/create.js') }}" type="text/javascript"></script>
@stop