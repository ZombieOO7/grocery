    <a class=" view" href="{{ URL::signedRoute('emailTemplate.edit',['id'=>$emailTemplate->uuid]) }}" title="Edit">
        <i class="fas fa-pencil-alt"></i>
    </a>

    {{-- <a class="delete" href="javascript:;" id="{{$emailTemplate->uuid}}" data-table_name="email_template_table" data-url="{{route('emailTemplate.delete')}}" title="Delete">
        <i class="fas fa-trash-alt"></i>
    </a>

    @if($emailTemplate->status=='1')
        <a class=" active_inactive" href="javascript:;" id="{{$emailTemplate->uuid}}" data-url="{{ route('emailTemplate.active_inactive', [$emailTemplate->id]) }}" data-table_name="email_template_table" title="Active">
            <i class="fas fa-toggle-on"></i>
        </a>
    @else
        <a class=" active_inactive" href="javascript:;" id="{{$emailTemplate->uuid}}" data-url="{{ route('emailTemplate.active_inactive', [$emailTemplate->id]) }}" data-table_name="email_template_table" title="Inactive">
            <i class="fas fa-toggle-off"></i>
        </a>
    @endif --}}
