@component('mail::message')

[This email is sent to data removal requester and site administrator]

Dear {{ $removalRequest->requester->name }},

We have received your request to remove everything for institution {{ $removalRequest->organisation->name }} on {{ $removalRequest->requested_at }}.
We are sorry to hear about this. And we understand that this must be a serious decision.

Data removal will completely remove everything of your institution.
Including all portfolios, projects, assessments, custom principles, institutional members, etc.

We provide a 30 days cool down period after receiving your request.
Please kindly note that your request will be processed after 30 days, exclusively with your final confirmation.

We will send your an email reminder 7 days before the tentative data removal date {{ $tentativeDataRemovalDate }}.
Please kindly reply email to indiciate your final decision.
You can either CANCEL or FINAL CONFIRM your data removal request.

We will perform data removal operation only with your final confirmation.

If you would like to cancel your data removal request, you can simply let us know by replying this email.

We hope to hear from you soon.

Thanks,<br>
{{ config('app.name') }}

@endcomponent
