
{{ Str::limit(@$subCategory->title, 30) }}
@if (strlen(@$subCategory->title) >= 30)
    <a href="javascript:void(0);" class="shw-dsc" data-title="{{ $title }}"  data-description="{{ @$subCategory->title }}" data-toggle="modal" data-target="#DescModal">{{ __('formname.read_more') }}</a>
@endif