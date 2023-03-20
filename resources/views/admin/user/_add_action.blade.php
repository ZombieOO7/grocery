@if((\Auth::guard('admin')->user()->can('user edit')))
<a class=" view" href="{{ URL::signedRoute('user_edit',['id'=>$user->uuid]) }}" title="Edit User"><i
        class="fas fa-pencil-alt"></i></a>
@endif
@if((\Auth::guard('admin')->user()->can('user delete')))
<a class=" delete" href="javascript:;" id="{{$user->id}}" data-table_name="user_table"
    data-url="{{route('user_delete')}}" data-job_url={{route('user.job')}} title="Delete User"><i class="fas fa-trash-alt"></i>
</a>
@endif
@if((\Auth::guard('admin')->user()->can('user active inactive')))
@if($user->status=='1')
<a class=" active_inactive" href="javascript:;" id="{{$user->id}}"  data-job_url={{route('user.job')}} data-url="{{ route('user_active_inactive', [$user->id]) }}" data-table_name="user_table"  title="Active User"><i class="fas fa-toggle-on"></i>
</a>

@else
<a class=" active_inactive" href="javascript:;" id="{{$user->id}}" data-job_url={{route('user.job')}} data-url="{{ route('user_active_inactive', [$user->id]) }}" data-table_name="user_table"  title="Inactive User"><i
        class="fas fa-toggle-off"></i>
</a>
@endif
{{-- @if((\Auth::guard('admin')->user()->can('user detail'))) --}}
<a class=" detail" href="{{URL::signedRoute('user_detail',['id'=>@$user->id])}}" id="{{$user->id}}" data-table_name="user_table" title="User Detail"><i class="fas fa-eye"></i>
</a>
{{-- @endif --}}
@endif