    <a class=" view" href="{{ URL::signedRoute('category.edit',['uuid'=>@$category->uuid]) }}" title="Edit">
        <i class="fas fa-pencil-alt"></i>
    </a>
    <a class="delete" href="javascript:;" id="{{@$category->uuid}}" data-table_name="category_table" data-url="{{route('category.delete')}}" title="Delete">
        <i class="fas fa-trash-alt"></i>
    </a>
    @if(@$category->status=='1')
        <a class=" active_inactive" href="javascript:;" id="{{@$category->uuid}}" data-url="{{ route('category.active_inactive', [@$category->id]) }}" data-table_name="category_table" title="Active">
            <i class="fas fa-toggle-on"></i>
        </a>
    @else
        <a class=" active_inactive" href="javascript:;" id="{{@$category->uuid}}" data-url="{{ route('category.active_inactive', [@$category->id]) }}" data-table_name="category_table" title="Inactive">
            <i class="fas fa-toggle-off"></i>
        </a>
    @endif