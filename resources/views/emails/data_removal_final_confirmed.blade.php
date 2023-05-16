@component('mail::message')

[This email is sent to data removal requester and site administrator]

Dear {{ $removalRequest->requester->name }},

We have received your request to remove everything for institution {{ $removalRequest->organisation->name }} on {{ $removalRequest->requested_at }}.
We are sorry to hear about this. And we understand that this must be a serious decision.

Data removal will completely remove everything of your institution.
Including all portfolios, projects, assessments, custom principles, institutional members, etc.

Thank you for informing us that you have FINAL CONFIRMED this data removal request.
That means everything of your institution will be removed.

The tentative data removal date is {{ $tentativeDataRemovalDate }}.
Please feel free to contact us before this date if you want to cancel your data removal request.

You will receive an email notification after data removal process is completed.

Thanks,<br>
{{ config('app.name') }}

@endcomponent
