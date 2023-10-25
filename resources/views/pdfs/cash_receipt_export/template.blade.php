<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>

<table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; border: black" cellpadding="8">
    <tbody>
    <tr>
        <td style="font-weight: bold" width="50%"  >TIPO </td>
        <td width="50%">{{$data->type === 'export' ? 'Exportaciones' : 'NACIONAL'}}</td>
    </tr>
    <tr>
        @if($data->type === 'export')
            <td style="font-weight: bold" width="50%" >TRM FECHA PAGO</td>
            <td width="50%"> {{moneyFormat($data->trm)}}</td>
        @endif
    </tr>
    <tr>
        <td style="font-weight: bold" width="50%">CLIENTE</td>
        <td width="50%" >{{$data->customer}}</td>
    </tr>
    <tr>
        <td style="font-weight: bold" width="50%">FECHA DE CREACION</td>
        <td width="50%">{{$data->created_at}}</td>
    </tr>

    <tr>
        <td style="font-weight: bold" width="50%">FECHA DE CONSIGNACION</td>
        <td width="50%">{{$data->payment_date}}</td>
    </tr>
    <tr>
        <td style="font-weight: bold" width="50%" >TOTAL RC</td>
        @if($data->type === 'export')
        <td width="50%">US{{$data->total_paid}}</td>

        @else
        <td width="50%">{{$data->total_paid}}</td>
        @endif

    </tr>
    <tr>
       <td style="font-weight: bold" width="50%">COMENTARIO</td>
       <td width="50%">{{$data->comments}}</td>
    </tr>
    </tbody>
</table>

<table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; border: black; margin-top: 20px" cellpadding="8">
    <thead>
    <tr>
        @if($data->type === 'export')
            <td>FACTURA</td>
            <td>TRM</td>
            <td>BRUTO</td>
            <td>DESCUENTO</td>
            <td>OTRAS DEDUCCIONES</td>
            <td>OTROS INGRESOS</td>
            <td>GASTOS BANCARIOS</td>
            <td>TOTAL</td>
            <td>DIFERENCIA EN CAMBIO</td>
            <td>SALDO A FAVOR</td>
        @else
            <td>FACTURA</td>
            <td>BRUTO</td>
            <td>DESCUENTO</td>
            <td>RETENCION</td>
            <td>RETEIVA</td>
            <td>RETEICA</td>
            <td>OTRAS DEDUCCIONES</td>
            <td>OTROS INGRESOS</td>
            <td>TOTAL</td>
        @endif
    </tr>
    </thead>
    <tbody>
    @foreach($data->details as $row)
    <tr>
        @if($data->type === 'export')
            <td align="center" width="11%">{{$row->invoice}}</td>
            <td align="right" width="11%">{{moneyFormat($row->trm)}}</td>
            <td align="right" width="11%">US{{$row->bruto}}</td>
            <td align="right" width="11%">US{{$row->discount}}</td>
            <td align="right" width="11%">US{{$row->other_deductions}}</td>
            <td align="right" width="11%">US{{$row->other_income}}</td>
            <td align="right" width="11%">US{{$row->financial_expenses}}</td>
            <td align="right" width="11%">US{{$row->total}}</td>
            <td align="right" width="11%">{{moneyFormat(round($row->change_difference))}}</td>
            <td align="right" width="11%">US{{$row->positive_balance}}</td>
        @else
            <td align="center" width="11%">{{$row->invoice}}</td>
            <td align="right" width="11%">{{moneyFormat($row->bruto)}}</td>
            <td align="right" width="11%">{{moneyFormat($row->discount)}}</td>
            <td align="right" width="11%">{{moneyFormat($row->retention)}}</td>
            <td align="right" width="11%">{{moneyFormat($row->reteiva)}}</td>
            <td align="right" width="11%">{{moneyFormat($row->reteica)}}</td>
            <td align="right" width="11%">{{moneyFormat($row->other_deductions)}}</td>
            <td align="right" width="11%">{{moneyFormat($row->other_income)}}</td>
            <td align="right" width="11%">{{moneyFormat($row->total)}}</td>
        @endif
    </tr>
    @endforeach
    </tbody>
</table>

</body>
</html>
