    <a class=" view" href="{{ URL::signedRoute('faq.edit',['uuid'=>@$faq->uuid]) }}" title="Edit">
        <i class="fas fa-pencil-alt"></i>
    </a>
    <a class="delete" href="javascript:;" id="{{@$faq->uuid}}" data-table_name="faq_table" data-url="{{route('faq.delete')}}" title="Delete">
        <i class="fas fa-trash-alt"></i>
    </a>
    @if(@$faq->status=='1')
        <a class=" active_inactive" href="javascript:;" id="{{@$faq->uuid}}" data-url="{{ route('faq.active_inactive', [@$faq->id]) }}" data-table_name="faq_table" title="Active">
            <i class="fas fa-toggle-on"></i>
        </a>
    @else
        <a class=" active_inactive" href="javascript:;" id="{{@$faq->uuid}}" data-url="{{ route('faq.active_inactive', [@$faq->id]) }}" data-table_name="faq_table" title="Inactive">
            <i class="fas fa-toggle-off"></i>
        </a>
    @endif