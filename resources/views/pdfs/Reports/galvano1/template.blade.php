<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>

<table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; border: black" cellpadding="8">
    <thead>
    <tr>
        <td>OP</td>
        <td>REFERENCIA</td>
        <td>PRODUCTO</td>
        <td>ACABADO</td>
        <td>MARCA</td>
        <td>ARTE</td>
        <td>CANT. PENDIENTE</td>
        <td>DIAS OV</td>
        <td>DIAS CT</td>
    </tr>
    </thead>
    <tbody>
    <!-- ITEMS HERE -->

    @if(count($data) > 0)
        @foreach($data as $key => $row)
            <tr>
                <td align="left">{{ $row->OP }}</td>
                <td align="left">{{ $row->REFERENCIA }}</td>
                <td align="left">{{ $row->PRODUCTO }}</td>
                <td align="left">{{ $row->ACABADO }}</td>
                <td align="left">{{ $row->MARCA }}</td>
                <td align="left">{{ $row->ARTE }}</td>
                <td align="right">{{ $row->CANT_PENDIENTE }}</td>
                <td align="right">{{ $row->DIAS_OV }}</td>
                <td align="right">{{ $row->DIAS_CT }}</td>
            </tr>
        @endforeach
    @else
        <tr>
            <td class="totals cost" align="center" colspan="5" style="color: #da001b">
                <b>NO HAY INFORMACIÓN DISPONIBLE</b>
            </td>
        </tr>
    @endif

    </tbody>
</table>

</body>
</html>
