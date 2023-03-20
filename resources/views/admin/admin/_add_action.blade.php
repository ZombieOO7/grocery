@if(@$admin->id != '1')
@if((\Auth::guard('admin')->user()->can('admin edit')))
<a class=" view" href="{{ URL::signedRoute('admin_edit',['id'=>@$admin->id]) }}" title="Edit Admin"><i
        class="fas fa-pencil-alt"></i></a>
@endif
@if((\Auth::guard('admin')->user()->can('admin delete')))
<a class="delete" href="javascript:;" id="{{@$admin->id}}" data-table_name="admin_table"
    data-url="{{route('admin_delete')}}" title="Delete Admin"><i class="fas fa-trash-alt"></i>
</a>
@endif
@if((\Auth::guard('admin')->user()->can('admin active inactive')))
@if(@$admin->status=='1')
<a class=" active_inactive" href="javascript:;" id="{{@$admin->id}}"  data-url="{{ route('admin_active_inactive', [@$admin->id]) }}" data-table_name="admin_table" title="Active Admin"><i class="fas fa-toggle-on"></i>
</a>
@else
<a class=" active_inactive" href="javascript:;" id="{{@$admin->id}}" data-url="{{ route('admin_active_inactive', [@$admin->id]) }}" data-table_name="admin_table" title="Inactive Admin"><i
        class="fas fa-toggle-off"></i>
</a>
@endif
@endif
@endif