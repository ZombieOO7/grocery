
{{ Str::limit(@$name, 20) }}
@if (strlen(@$name) >= 20)
    <a href="javascript:void(0);" class="shw-dsc" data-title="{{ @$title }}"  data-description="{{ @$name }}" data-toggle="modal" data-target="#DescModal">{{ __('formname.read_more') }}</a>
@endif