
{{ Str::limit(@$location->title, 30) }}
@if (strlen(@$location->title) >= 30)
    <a href="javascript:void(0);" class="shw-dsc"  data-description="{{ @$location->title }}" data-toggle="modal" data-target="#DescModal">{{ __('formname.read_more') }}</a>
@endif