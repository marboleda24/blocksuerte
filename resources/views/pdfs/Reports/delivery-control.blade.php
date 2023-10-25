<table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; border: black" cellpadding="8">
    <thead>
    <tr>
        <td align="center">#</td>
        @foreach($data->columns as $key => $value)
            <td align="center">{{$value}}</td>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @php
        $total = 0;
        $total_kilos = 0;
    @endphp
    @foreach($query as $key => $item)
        {{ $total+= $item->CANT }}
        <tr>
            <td align="center">{{$key+1}}</td>
            @foreach($data->columns as $key => $value)
                @if($key === 'CANT')
                    <td align="right">{{number_format($item->$key, 0, '.', '')}}</td>
                @elseif($key === 'RESPONSABLE')
                    {{ $total_kilos += floatval($item->$key)}}
                    <td align="right">{{number_format(floatval($item->$key), 2, '.', '')}}</td>
                @else
                    <td>{{$item->$key}}</td>
                @endif
            @endforeach
        </tr>
    @endforeach
    <tr>
        <td align="right" colspan="{{ $total_kilos > 0 ? count((array)$data->columns)-1 : count((array)$data->columns) }}" style="font-weight: bold">
            TOTAL
        </td>
        <td align="right" style="font-weight: bold">
            {{ number_format($total, 0, '.', '') }}
        </td>
        @if($total_kilos > 0)
            <td align="right" style="font-weight: bold">
                {{ number_format($total_kilos, 2, '.', '') }}
            </td>
        @endif
    </tr>
    </tbody>
</table>
