    <a class=" view" href="{{ URL::signedRoute('product.edit',['uuid'=>@$product->uuid]) }}" title="Edit">
        <i class="fas fa-pencil-alt"></i>
    </a>
    <a class="delete" href="javascript:;" id="{{@$product->uuid}}" data-table_name="product_table" data-url="{{route('product.delete')}}" title="Delete">
        <i class="fas fa-trash-alt"></i>
    </a>
    @if(@$product->status=='1')
        <a class=" active_inactive" href="javascript:;" id="{{@$product->uuid}}" data-url="{{ route('product.active_inactive', [@$product->id]) }}" data-table_name="product_table" title="Active">
            <i class="fas fa-toggle-on"></i>
        </a>
    @else
        <a class=" active_inactive" href="javascript:;" id="{{@$product->uuid}}" data-url="{{ route('product.active_inactive', [@$product->id]) }}" data-table_name="product_table" title="Inactive">
            <i class="fas fa-toggle-off"></i>
        </a>
    @endif