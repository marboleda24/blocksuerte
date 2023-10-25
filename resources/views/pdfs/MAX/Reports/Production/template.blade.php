<table class="table" style="width:100%">
    <thead>
        <tr>
            @foreach($headings as $key => $heading)
                <th align="center">{{ $heading }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @for($i = 0; $i < count($data); $i++)
            <tr>
                @foreach($headings as $key => $heading)
                    <td align="center">{{ $data[$i]->$key }}</td>
                @endforeach
            </tr>
        @endfor
    </tbody>
</table>

