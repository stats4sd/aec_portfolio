@component('mail::message')

{{ $invite->inviter->name }} ({{ $invite->inviter->email }}) has invited you to join the following institution on the {{ config('app.name') }}.

**Institution:** {{ $invite->organisation->name }}

Click the link below to register on the platform. If you use the same email address, you will be automatically added to the institution after registration.

@component('mail::button', ['url' => route('register').'?token='.$invite->token])
    Register to join {{ $invite->organisation->name }}
@endcomponent

If you do not wish to register, or you have been sent this email by mistake, please ignore this message.

Best regards,<br>
Site Admin,<br>
{{ config('app.name') }}

@endcomponent
