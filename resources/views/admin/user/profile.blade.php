@extends('admin.layouts.default')
@section('inc_css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.3/css/bootstrap-colorpicker.min.css"
    rel="stylesheet">
<style>
    label {
        font-weight: 600;
    }
    .hid_spn{
        display: none !important;
    }
</style>
@endsection
@section('content')
@php
if(isset($user)){
$title=__('formname.user_detail');
}
else{
$title=__('formname.user_detail');
}
if(URL::previous() == Request::fullUrl()){
    $backurl = route('user_index');
}else{
    $backurl = URL::previous();
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
                                <a href="{{$backurl}}"
                                    class="btn btn-secondary m-btn m-btn--air m-btn--custom">
                                    <span>
                                        <i class="la la-arrow-left"></i>
                                        <span>Back</span>
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        <div class="m-wizard__form-step m-wizard__form-step--current" id="web_setting_form_step">
                            <div class="row">
                                <div class="col-xl-12">
                                    <ul class="nav nav-tabs m-tabs-line--2x m-tabs-line m-tabs-line--danger"
                                        role="tablist">
                                        <li class="nav-item m-tabs__item">
                                            <a class="nav-link m-tabs__link active" data-toggle="tab" href="#profile"
                                                role="tab">Profile Info</a>
                                        </li>
                                        <li class="nav-item m-tabs__item">
                                            <a class="nav-link m-tabs__link" data-toggle="tab" href="#job"
                                                role="tab">Job Detail</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content m--margin-top-40">
                                        <div class="tab-pane active" id="profile" role="tabpanel">
                                            <div class="m-form__section m-form__section--first">
                                                <div class="form-group m-form__group row">
                                                    {!! Form::label(__('formname.first_name').' : ',
                                                    null,['class'=>'col-form-label
                                                    col-lg-3
                                                    col-sm-12']) !!}
                                                    <div class="col-lg-6 col-md-9 col-sm-12 col-form-label">
                                                        {{@$user->first_name}}
                                                    </div>
                                                </div>
                                                <div class="form-group m-form__group row">
                                                    {!! Form::label(__('formname.last_name').' : ',
                                                    null,['class'=>'col-form-label
                                                    col-lg-3 col-sm-12']) !!}
                                                    <div class="col-lg-2 col-md-9 col-sm-12 col-form-label">
                                                        {{@$user->last_name}}
                                                    </div>
                                                </div>
                                                <div class="form-group m-form__group row">
                                                    {!! Form::label(__('formname.email').' : ',
                                                    null,['class'=>'col-form-label
                                                    col-lg-3
                                                    col-sm-12']) !!}
                                                    <div class="col-lg-6 col-md-9 col-sm-12 col-form-label">
                                                        {{@$user->email}}
                                                    </div>
                                                </div>
                                                <div class="form-group m-form__group row">
                                                    {!! Form::label(__('formname.phone').' : ',
                                                    null,['class'=>'col-form-label
                                                    col-lg-3 col-sm-12']) !!}
                                                    <div class="col-lg-2 col-md-9 col-sm-12 col-form-label">
                                                        {{@$user->phone}}
                                                    </div>
                                                </div>
                                                <div class="form-group m-form__group row">
                                                    {!! Form::label(__('formname.company.title').' : ',
                                                    null,['class'=>'col-form-label
                                                    col-lg-3
                                                    col-sm-12']) !!}
                                                    <div class="col-lg-6 col-md-9 col-sm-12 col-form-label">
                                                        {{@$user->company->title}}
                                                    </div>
                                                </div>
                                                <div class="form-group m-form__group row">
                                                    {!! Form::label(__('formname.company_position').' : ',
                                                    null,['class'=>'col-form-label
                                                    col-lg-3
                                                    col-sm-12']) !!}
                                                    <div class="col-lg-6 col-md-9 col-sm-12 col-form-label">
                                                        {{@config('constant.user_types')[$user->user_type]}}
                                                    </div>
                                                </div>
                                                {{-- <div class="form-group m-form__group row">
                                                    {!! Form::label(__('formname.company_position').' : ',
                                                    null,['class'=>'col-form-label
                                                    col-lg-3 col-sm-12']) !!}
                                                    <div class="col-lg-2 col-md-9 col-sm-12 col-form-label">
                                                        {!! Form::select('user_type',@$userTypeList,@$user->user_type,['data-id'=>@$user->uuid,'data-url'=>route('user.change-position'),'class' => 'form-control positionId','id'=>'user_type']) !!}
                                                    </div>
                                                </div> --}}
                                                {{-- <div class="form-group m-form__group row">
                                                    {!! Form::label(__('formname.role_id').' : ',
                                                    null,['class'=>'col-form-label
                                                    col-lg-3 col-sm-12']) !!}
                                                    <div class="col-lg-2 col-md-9 col-sm-12 col-form-label">
                                                        {!! Form::select('role_id',@$subRoleList,@$user->role_id,['data-id'=>@$user->uuid,'data-url'=>route('user.change-role'),'class' => 'form-control','id'=>'roleId']) !!}
                                                    </div>
                                                </div> --}}
                                                <div class="form-group m-form__group row">
                                                    {!! Form::label(__('formname.profile_picture') .' : ',
                                                    null,['class'=>'col-form-label
                                                    col-lg-3 col-sm-12'])
                                                    !!}
                                                    <div class="col-lg-6 col-md-9 col-sm-12">
                                                        {{-- @if(file_exists(base_path(@$user->path)) && @$user->path !=
                                                        null) --}}
                                                        <img id="blah" src="{{@$user->profile_image }}" alt=""
                                                            height="100px;" width="100px;" style="display:block;" />
                                                        {{-- @else
                                                        <img id="blah" src="{{asset('images/default.png') }}" alt=""
                                                            height="100px;" width="100px;" style="display:block;}}" />
                                                        @endif --}}
                                                    </div>
                                                </div>
                                                <div class="form-group m-form__group row">
                                                    {!! Form::label(__('formname.register_as_user').' : ',
                                                    null,['class'=>'col-form-label
                                                    col-lg-3
                                                    col-sm-12']) !!}
                                                    <div class="col-lg-6 col-md-9 col-sm-12 col-form-label">
                                                        {{@$user->register_as_user_text}}
                                                    </div>
                                                </div>
                                                @if((\Auth::guard('admin')->user()->can('user active inactive')))
                                                <div class="form-group m-form__group row">
                                                    {!! Form::label('Active/Inactive : ', null,['class'=>'col-form-label
                                                    col-lg-3
                                                    col-sm-12']) !!}
                                                    <div class="col-lg-3 col-md-9 col-sm-12">
                                                        @if(@$user->status=='1')
                                                        <a class="cd-stts active_inactive_user userStatus"
                                                            href="javascript:;" id="{{@$user->id}}"
                                                            data-url="{{ route('user_active_inactive', [@$user->id]) }}"
                                                            data-status={{@$user->status}} data-table_name="user_table"
                                                            title="Active User">
                                                            <i class="fas fa-toggle-on" id="tggl-clss"
                                                                style="font-size: 25px;"></i>
                                                        </a>
                                                        @else
                                                        <a class="cd-stts active_inactive_user userStatus"
                                                            href="javascript:;" id="{{@$user->id}}"
                                                            data-url="{{ route('user_active_inactive', [@$user->id]) }}"
                                                            data-status={{@$user->status}} data-table_name="user_table"
                                                            title="Inactive User">
                                                            <i class="fas fa-toggle-off" id="tggl-clss"
                                                                style="font-size: 25px;"></i>
                                                        </a>
                                                        @endif
                                                    </div>
                                                </div>
                                                @endif
                                                @if((\Auth::guard('admin')->user()->can('user delete')))
                                                <div class="form-group m-form__group row">
                                                    {!! Form::label('Delete User : ', null,['class'=>'col-form-label
                                                    col-lg-3
                                                    col-sm-12']) !!}
                                                    <div class="col-lg-3 col-md-9 col-sm-12">
                                                        <a class="deleteUser" href="javascript:;" id="{{@$user->id}}"
                                                            data-table_name="user_table"
                                                            data-url="{{route('user_delete')}}" title="Delete User">
                                                            <i class="fas fa-trash-alt" id="tggl-clss"
                                                                style="font-size: 25px;"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="job" role="tabpanel">
                                            <div class="m-form__section m-form__section--first">
                                                {{-- <div class="form-group m-form__group row">
                                                </div> --}}
                                                <table
                                                    class="table table-striped- table-bordered table-hover table-checkable for_wdth"
                                                    id="job_table" data-type="" data-url="{{ route('job.datatable') }}">
                                                    <thead>
                                                        <tr>
                                                            {{-- <th>{{__('formname.job.title')}}</th> --}}
                                                            <th>{{__('formname.job.machine')}}</th>
                                                            <th>{{__('formname.job.problem')}}</th>
                                                            <th>{{__('formname.job.location')}}</th>
                                                            <th>{{__('formname.job.requested_by')}}</th>
                                                            <th>{{__('formname.job.priority')}}</th>
                                                            <th>{{__('formname.job.job_status')}}</th>
                                                            <th>{{__('formname.created_at')}}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($jobs as $job)
                                                            <tr>
                                                                {{-- <td>{{@$job->title}}</td> --}}
                                                                <td>{{@$job->machine->title}}</td>
                                                                <td>{{@$job->problem->title}}</td>
                                                                <td>{{@$job->location->title}}</td>
                                                                <td>{{@$job->created_by_text}}</td>
                                                                <td>{{@config('constant.priorites')[$job->priority]}}</td>
                                                                <td>{{@config('constant.job_status_text')[$job->job_status_id]}}</td>
                                                                <td>{!! @$job->proper_created_at !!}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="7" class="text-center">No jobs found</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="m-portlet__foot m-portlet__foot--fit">
                            <div class="m-form__actions m-form__actions">
                                <br>
                                <div class="row">
                                    <div class="col-lg-9 ml-lg-auto">
                                        <a href="{{Route('user_index')}}"
                                            class="btn btn-info">{{__('formname.back')}}</a>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@section('inc_script')
<script>
    var returnUrl="{{Route('user_index')}}";
    var jobCheckUrl = "{{route('user.job')}}";
    var getRoleUrl = "{{route('get-user-role')}}";
</script>
<script src="{{ asset('backend/js/user/create.js') }}" type="text/javascript"></script>
@endsection