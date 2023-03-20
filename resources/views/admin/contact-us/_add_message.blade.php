
{{ Str::limit(@$contact->message, 30) }}
@if (strlen(@$contact->message) >= 30)
    <a href="javascript:void(0);" class="shw-dsc" data-subject="{{ @$contact->subject }}" data-description="{{ @$contact->message }}" data-toggle="modal" data-target="#DescModal">{{ __('formname.read_more') }}</a>
@endif