<x-mail::message>
# {{ __('site.contact.mail_title') }}

**{{ __('site.contact.topic') }}:** {{ $contact->topic }}
**{{ __('site.contact.name') }}:** {{ $contact->name }}
**{{ __('site.contact.email') }}:** {{ $contact->email }}
@if ($contact->phone)
**{{ __('site.contact.phone') }}:** {{ $contact->phone }}
@endif

{{ $contact->message }}

<x-mail::button :url="url('/admin/contact-messages/'.$contact->id)">
{{ __('site.contact.mail_open') }}
</x-mail::button>
</x-mail::message>
