<table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; border: black" cellpadding="8">
<thead>
    <tr>
        <td align="center">ORDEN</td>
        <td align="center">OV</td>
        <td align="center" colspan="2">PRODUCTO</td>
        <td align="center">LOTE</td>
        <td align="center">CANTIDAD</td>
        <td align="center">TERMINADO</td>
        <td align="center" colspan="2">RESPONSABLE</td>
    </tr>
    </thead>
    <tbody>
    @php
        $total = 0;
    @endphp
    @foreach($data as $key => $item)
        {{$total+= $item->CANT }}
        <tr>
            <td align="center">{{$key+1}}</td>
            <td>{{$item->OP}}</td>
            <td>{{$item->OV}}</td>
            <td>{{$item->PRODUCTO}}</td>
            <td>{{$item->LOTE}}</td>
            <td align="right">{{number_format($item->CANT, 2, '.', '')}}</td>
            <td>{{$item->ACABADO_GALV}}</td>
            <td>{{$item->RESPONSABLE}}</td>
            <td>{{$item->COD_RESP}}</td>
        </tr>
    @endforeach
    <tr>
        <td align="right" colspan="5" style="font-weight: bold">
            TOTAL
        </td>
        <td align="right" style="font-weight: bold">
            {{ number_format($total, 2, '.', '') }}
        </td>
    </tr>

    </tbody>

</table>

