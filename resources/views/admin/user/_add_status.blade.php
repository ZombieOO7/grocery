@if($user->status == 0)
<span class="m-badge  m-badge--danger m-badge--wide">{{trans('Inactive')}}</span>
@else
<span class="m-badge  m-badge--success m-badge--wide">{{trans('Active')}}</span>
@endif