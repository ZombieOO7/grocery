
{!! Str::limit(@$faq->content, 60) !!}
@if (strlen(@$faq->content) >= 60)
    <a href="javascript:void(0);" class="shw-dsc" data-backdrop="static" data-keyboard="false" data-description="{{ @$faq->content }}" data-toggle="modal" data-target="#DescModal">{{ __('formname.read_more') }}</a>
@endif