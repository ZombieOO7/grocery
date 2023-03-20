    <a class=" view" data-job_url="{{ route('location.jobstatus') }}" href="{{ URL::signedRoute('location.edit',['uuid'=>@$location->uuid]) }}" title="Edit">
        <i class="fas fa-pencil-alt"></i>
    </a>
    <a class="delete" data-job_url="{{ route('location.jobstatus') }}" href="javascript:;" id="{{@$location->uuid}}" data-table_name="location_table" data-url="{{route('location.delete')}}" title="Delete">
        <i class="fas fa-trash-alt"></i>
    </a>
    @if(@$location->status=='1')
        <a class=" active_inactive" data-job_url="{{ route('location.jobstatus') }}" href="javascript:;" id="{{@$location->uuid}}" data-url="{{ route('location.active_inactive', [@$location->id]) }}" data-table_name="location_table" title="Active">
            <i class="fas fa-toggle-on"></i>
        </a>
    @else
        <a class=" active_inactive" data-job_url="{{ route('location.jobstatus') }}" href="javascript:;" id="{{@$location->uuid}}" data-url="{{ route('location.active_inactive', [@$location->id]) }}" data-table_name="location_table" title="Inactive">
            <i class="fas fa-toggle-off"></i>
        </a>
    @endif