<div class="card mt-3">
    <div class="card-header">
        <h4 class="card-title">Redlines</h4>
    </div>
    <div class="card-body">
        <table class="table table-borderless">
            <tr>
                <th>Redline</th>
                <th>% Projects that did not pass</th>
            </tr>
            @foreach($redlines as $redline)
                <tr>
                    <td>{{ $redline->name }}</td>
                    <td>{{ $assessedProjects->count() > 0 ? ($redline->failingProjects->count() / $assessedProjects->count()) * 100 : "-"}}
                        %
                    </td>
                </tr>

            @endforeach
        </table>
    </div>
</div>
