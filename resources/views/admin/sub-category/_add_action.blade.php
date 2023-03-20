    <a class=" view" href="{{ URL::signedRoute('subcategory.edit',['uuid'=>@$subCategory->uuid]) }}" title="Edit">
        <i class="fas fa-pencil-alt"></i>
    </a>
    <a class="delete" href="javascript:;" id="{{@$subCategory->uuid}}" data-table_name="sub_category_table" data-url="{{route('subcategory.delete')}}" title="Delete">
        <i class="fas fa-trash-alt"></i>
    </a>
    @if(@$subCategory->status=='1')
        <a class=" active_inactive" href="javascript:;" id="{{@$subCategory->uuid}}" data-url="{{ route('subcategory.active_inactive', [@$subCategory->id]) }}" data-table_name="sub_category_table" title="Active">
            <i class="fas fa-toggle-on"></i>
        </a>
    @else
        <a class=" active_inactive" href="javascript:;" id="{{@$subCategory->uuid}}" data-url="{{ route('subcategory.active_inactive', [@$subCategory->id]) }}" data-table_name="sub_category_table" title="Inactive">
            <i class="fas fa-toggle-off"></i>
        </a>
    @endif