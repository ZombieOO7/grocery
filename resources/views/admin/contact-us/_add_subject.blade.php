{{ Str::limit(@$contact->subject, 30) }}
@if (strlen(@$contact->subject) >= 30)
    <a href="javascript:void(0);" class="shw-dsc" data-subject="{{ @$contact->subject }}" data-description="{{ @$contact->subject }}" data-toggle="modal" data-target="#SubjModal">{{ __('formname.read_more') }}</a>
@endif