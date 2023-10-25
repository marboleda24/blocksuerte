<table class="table" style="width:100%">
    <thead>
        <tr>
            <th align="center">ORDEN</th>
            <th align="center">OV/REFERENCIA </th>
            <th align="center">PRODUCTO</th>
            <th align="center">LOTE/ARTE </th>
            <th align="center">MARCA</th>
            <th align="center">CANTIDAD</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $key => $group)
            @php
                $total = 0;
            @endphp
            <tr>
                <td colspan="6" style="font-weight: bold">{{$key}}</td>
            </tr>
            @foreach($group as $item)
                {{$total+= $item->CANT}}
                <tr>
                    <td>{{$item->OP}}</td>
                    <td>{{$item->OV}}</td>
                    <td>{{$item->PRODUCTO}}</td>
                    <td>{!! $item->LOTE ?? $item->LOTE . ' – ' !!} {{$item->ARTE}}</td>
                    <td>{{$item->Marca}}</td>
                    <td align="right">{{number_format($item->CANT, 2, '.', '')}}</td>
                </tr>
            @endforeach
        @endforeach

    </tbody>

</table>

