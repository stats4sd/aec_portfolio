<div class="card">
    <div class="card-header">
        <h3 class="card-title">Key Indicators at a Glance</h3>
    </div>
    <div class="card-body">
        <table class="table table-borderless">
            <tr>
                <th class="text-right mr-4">Overall Portfolio Score</th>
                <td>{{$overallScore}}%</td>
            </tr>
            <tr>
                <th class="text-right mr-4">Number of Projects Assessed</th>
                <td>{{ $count }}</td>
            </tr>
            <tr>
                <th class="text-right mr-4">Total Budget for the period</th>
                <td>{{  number_format($totalBudget)}} USD</td>
            </tr>
            <tr>
                <th class="text-right mr-4">Agroecology Directed Budget<span
                            class="text-primary">*</span>
                </th>
                <td>{{ number_format($agroecologyBudget) }} USD</td>
            </tr>
        </table>
        <span class="text-sm-left font-xs"><span class="text-primary">*</span> <span
                    class="text-secondary">This is calculated as the overall portfolio score times the total budget</span></span>
        <div class="text-right mt-2">
            <a href="{{ route('organisation.export', ['organisation' => $organisation]) }}"
               class="btn btn-sm btn-primary"> Export Organisation Project Data</a>

        </div>

    </div>
</div>
