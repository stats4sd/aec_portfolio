<div class="">
    <table class="table table-striped">
        <thead>
        <td>Assessment completion date</td>
        <td>Overall score</td>
        <td>Assessment status</td>
        <td>Actions</td>
        </thead>
        <tbody>
        @foreach ($entry->assessments as $assessment)
            <tr>
                <td>{{ $assessment->completed_at }}</td>
                <td>{{ $assessment->overall_score }}</td>
                <td>{{ $assessment->assessment_status }}</td>
                @if($assessment->hasRevisions())
                    <td><a href="{{ url('admin/assessment/'.$assessment->id.'/revise') }}"><i class="la la-recycle"></i> Show Revisions</a></td>
                @endif
                <td><a href="{{ url('admin/assessment/'.$assessment->id.'/show') }}" class="btn btn-sm btn-link" data-toggle="popover"><i class="la la-eye"></i> Preview</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
</div>
