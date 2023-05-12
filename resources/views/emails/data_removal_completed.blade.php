@component('mail::message')

[This email is sent to data removal requester and site administrator]

Dear {{ $removalRequest->requester_name }},

We have received your request to remove everything for institution {{ $removalRequest->organisation_name }} on {{ $removalRequest->requested_at }}.
We are sorry to hear about this. And we understand that this must be a serious decision.

Data removal will completely remove everything of your institution.
Including all portfolios, projects, assessments, custom principles, institutional members, etc.

Please note that data removal process is fully completed.
Everything of your institution have been removed.

Thanks,<br>
{{ config('app.name') }}

@endcomponent
