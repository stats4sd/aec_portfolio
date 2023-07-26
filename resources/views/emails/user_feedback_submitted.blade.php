<x-mail::message>
# {{ $feedback->type->name }} submitted for the {{ config('app.name') }}

Someone has submitted a new piece of feedback:

    - **Type**: {{ $feedback->type->name }}
    - **Message**: {{  $feedback->message }}

<x-mail::button :url="backpack_url('user-feedback')">
Review Feedback
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
