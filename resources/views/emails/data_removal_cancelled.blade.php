@component('mail::message')

[This email is sent to data removal requester and site administrator]

Dear {{ $removalRequest->requester->name }},

We have received your request to remove everything for institution {{ $removalRequest->organisation->name }} on {{ $removalRequest->requested_at }}.

Thank you for informing us that you decided to CANCEL this data removal request.
That means everything of your institution will remain.

Thanks,<br>
{{ config('app.name') }}

@endcomponent