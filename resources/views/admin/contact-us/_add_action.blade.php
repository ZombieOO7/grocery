@if((\Auth::guard('admin')->user()->can('contact us edit')))
{{-- <a class=" view" href="{{ URL::signedRoute('contact_us_edit',['id'=>@$contact->id]) }}" title="Edit Contact Us"><i
        class="fas fa-pencil-alt"></i></a> --}}
@endif
{{-- @if((\Auth::guard('admin')->user()->can('contact us delete'))) --}}
<a class=" delete" href="javascript:;" id="{{@$contact->id}}"  data-table_name="contact_us_table"
    data-url="{{route('contact_us_delete')}}" title="Delete Contact Us"><i class="fas fa-trash-alt"></i>
</a>
<a class="detail" href="{{route('contact_us_detail',['uuid'=>@$contact->uuid])}}" id="{{@$contact->id}}" data-table_name="contact_us_table" title="Detail"><i class="fas fa-eye"></i>
</a>
{{-- @endif --}}