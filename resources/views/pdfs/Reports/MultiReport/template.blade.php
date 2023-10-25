<table class="table" style="width:100%">
    <thead>
        <tr>
            <th align="center">OP</th>
            <th align="center">OV</th>
            <th align="center">CODIGO PRODUCTO</th>
            <th align="center">DESCRIPCION PRODUCTO</th>
            <th align="center">REFERENCIA</th>
            <th align="center">ACABADO</th>
            <th align="center">LOTE</th>
            <th align="center">ARTE</th>
            <th align="center">MARCA</th>
            <th align="center">CANT COMPLETADA</th>
            <th align="center">CANT PENDIENTE</th>
            <th align="center">FECHA LIBERACION</th>
            <th align="center">DIAS PLANTA</th>
            <th align="center">DIAS OV</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $key => $item)
            <tr>
                <td>{{$item->OP}}</td>
                <td>{{$item->OV}}</td>
                <td>{{$item->COD_PROD}}</td>
                <td>{{$item->PRODUCTO}}</td>
                <td>{{$item->REFERENCIA}}</td>
                <td>{{$item->ACABADO}}</td>
                <td>{{$item->LOTE}}</td>
                <td>{{$item->ARTE_OV}}</td>
                <td>{{$item->MARCA}}</td>
                <td align="right">{{intval($item->CANT_COMPLETADA)}}</td>
                <td align="right">{{intval($item->CANT_PENDIENTE)}}</td>
                <td>{{\Carbon\Carbon::parse($item->FECHA_LIBERACION)->format('Y-m-d')}}</td>
                <td align="right">{{intval($item->FECHA_MOV)}}</td>
                <td align="right">{{intval($item->FECHA_INI)}}</td>
            </tr>
        @endforeach
    </tbody>
</table>

