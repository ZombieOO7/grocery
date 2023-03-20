
{{ Str::limit(@$product->title, 30) }}
@if (strlen(@$product->title) >= 30)
    <a href="javascript:void(0);" class="shw-dsc" data-title="{{ $title }}"  data-description="{{ @$product->title }}" data-toggle="modal" data-target="#DescModal">{{ __('formname.read_more') }}</a>
@endif