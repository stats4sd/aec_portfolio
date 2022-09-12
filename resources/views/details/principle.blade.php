<div class="">
    <table class="table table-striped">
        <thead>
            <td>Rating</td>
            <td>What the rating represents</td>
        </thead>
        <tbody>
        <tr>
            <td>na</td>
            <td>{{ $entry->can_be_na ? $entry->rating_na : 'Cannot be NA' }}</td>
        </tr>
            <tr>
                <td>0</td>
                <td>{{$entry->rating_zero}}</td>
            </tr>
                    <tr>
                <td>1</td>
                <td>{{$entry->rating_one}}</td>
            </tr>
                    <tr>
                <td>2</td>
                <td>{{$entry->rating_two}}</td>
            </tr>

        </tbody>
    </table>
    </div>

</div>
