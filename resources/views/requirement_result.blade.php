<table class="table table-bordered table-sm">
    <thead>
        <tr>
            <th class="whitespace-nowrap">ARTE</th>
            <th class="whitespace-nowrap">PRODUCTO</th>
        </tr>
    </thead>
    <tbody>
    @foreach($results as $result)
        <tr>
            <td>{{ $result['art'] }}</td>
            <td>{{ $result['product'] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
