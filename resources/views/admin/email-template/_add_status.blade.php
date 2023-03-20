@if($emailTemplate->status == 0)
    <span class="m-badge  m-badge--danger m-badge--wide">{{__('Inactive')}}</span>
@else
    <span class="m-badge  m-badge--success m-badge--wide">{{__('Active')}}</span>
@endif