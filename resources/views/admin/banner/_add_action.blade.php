    <a class=" view" href="{{ URL::signedRoute('banner.edit',['uuid'=>@$banner->uuid]) }}" title="Edit">
        <i class="fas fa-pencil-alt"></i>
    </a>
    <a class="delete" href="javascript:;" id="{{@$banner->uuid}}" data-table_name="banner_table" data-url="{{route('banner.delete')}}" title="Delete">
        <i class="fas fa-trash-alt"></i>
    </a>
    @if(@$banner->status=='1')
        <a class=" active_inactive" href="javascript:;" id="{{@$banner->uuid}}" data-url="{{ route('banner.active_inactive', [@$banner->id]) }}" data-table_name="banner_table" title="Active">
            <i class="fas fa-toggle-on"></i>
        </a>
    @else
        <a class=" active_inactive" href="javascript:;" id="{{@$banner->uuid}}" data-url="{{ route('banner.active_inactive', [@$banner->id]) }}" data-table_name="banner_table" title="Inactive">
            <i class="fas fa-toggle-off"></i>
        </a>
    @endif