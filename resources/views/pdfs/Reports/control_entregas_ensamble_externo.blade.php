<table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; border: black" cellpadding="8">
<thead>
    <tr>
        <td align="center">FECHA TRANSACCION</td>
        <td align="center">OP</td>
        <td align="center">PRODUCTO</td>
        <td align="center">LOTE</td>
        <td align="center">COD COMODIDAD</td>
        <td align="center">REFERENCIA</td>
        <td align="center">CANTIDAD</td>
    </tr>
    </thead>
    <tbody>
    @php
        $total = 0;
    @endphp
    @foreach($data as $key => $item)
        {{$total+= $item->CANT }}
        <tr>
            <td>{{$item->FECHA}}</td>
            <td>{{$item->OP}}</td>
            <td>{{$item->PRODUCTO}}</td>
            <td>{{$item->LOTE}}</td>
            <td>{{$item->TIPO_PRODUCTO}}</td>
            <td>{{$item->REFERENCIA}}</td>
            <td align="right">{{number_format($item->CANT, 0, '.', '')}}</td>
        </tr>
    @endforeach
        <tr>
            <td align="right" colspan="6" style="font-weight: bold">
                TOTAL
            </td>
            <td align="right" style="font-weight: bold">
                {{ number_format($total, 0, '.', '') }}
            </td>
        </tr>
    </tbody>
</table>
