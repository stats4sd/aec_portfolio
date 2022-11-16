<div class="card mt-3">
    <div class="card-header">
        <h4 class="card-title">Non Applicable Principles</h4>
    </div>
    <div class="card-body">
        <table class="table table-borderless">
            <tr>
                <th>Redline</th>
                <th>% Projects that marked a principle as Not Applicable</th>
            </tr>
            @foreach($principles as $principle)
                <tr>
                    <td>{{ $principle->name }}</td>
                    <td>{{ $naPrinciples[$principle->name] > 0 ? ($naPrinciples[$principle->name] / $passedProjects->count()) * 100 : 0}}
                        %
                    </td>
                </tr>

            @endforeach
        </table>
        <small>Only Includes projects that passed all redlines</small>

    </div>
</div>
