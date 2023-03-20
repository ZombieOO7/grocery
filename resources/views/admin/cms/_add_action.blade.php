{{-- @if((\Auth::guard('admin')->user()->can('page edit'))) --}}
<a class=" view" href="{{ URL::signedRoute('cms_edit',['uuid'=>@$cms->uuid]) }}" title="Edit Page"><i
        class="fas fa-pencil-alt"></i></a>
{{-- @endif --}}
{{-- @if((\Auth::guard('admin')->user()->can('page delete'))) --}}
{{-- <a class=" delete" href="javascript:;" id="{{@$cms->id}}" data-table_name="cms_table"
    data-url="{{route('cms_delete')}}" title="Delete Page"><i class="fas fa-trash-alt"></i>
</a> --}}
{{-- @if(@$cms->status=='1')
<a class=" active_inactive" href="javascript:;" id="{{@$cms->uuid}}" data-url="{{ route('cms_active_inactive') }}" data-table_name="cms_table" title="Active">
    <i class="fas fa-toggle-on"></i>
</a>
@else
<a class=" active_inactive" href="javascript:;" id="{{@$cms->uuid}}" data-url="{{ route('cms_active_inactive') }}" data-table_name="cms_table" title="Inactive">
    <i class="fas fa-toggle-off"></i>
</a>
@endif --}}
{{-- @endif --}}