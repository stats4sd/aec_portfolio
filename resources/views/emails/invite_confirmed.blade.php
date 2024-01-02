@component('mail::message')

{{ $inviterName }} ({{ $inviterEmail }}) has invited you to join the following institution on the {{ config('app.name') }}.

**Institution:** {{ $organisationName }}

As you are an existing user on the platform, no registration is required.
You have been added to {{ $organisationName }} directly.

If you have been sent this email by mistake, please ignore this message.

Best regards,<br>
Site Admin,<br>
{{ config('app.name') }}

@endcomponent
