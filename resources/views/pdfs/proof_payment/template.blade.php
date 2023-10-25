<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>
<table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; border: black" cellpadding="8">
    <thead>
    <tr>
        <td style="font-weight: bold">DESCRIPCIÓN</td>
        <td style="font-weight: bold">CANTIDAD</td>
        <td style="font-weight: bold">UM</td>
        <td style="font-weight: bold">PAGOS</td>
        <td style="font-weight: bold">DEDUCCIONES</td>
    </tr>
    </thead>
    <tbody>
    @foreach($items as $item)
        <tr>
            <td align="left">{{ $item->CONCEPTO.' - '.$item->DESCRIPCION_CONCEPTO }}</td>
            <td class="cost" align="right">{{ intval($item->HORAS) }}</td>
            <td align="center">H</td>
            <td class="cost" align="{{ $item->PAGO > 0 ? 'right' : 'center' }}">{{ $item->PAGO > 0 ? moneyFormat($item->PAGO) : '–' }}</td>
            <td class="cost" align="{{ $item->DEDUCCIONES > 0 ? 'right' : 'center' }}">{{ $item->DEDUCCIONES > 0 ? moneyFormat($item->DEDUCCIONES) : '–' }}</td>
        </tr>
    @endforeach

    <tr>
        <td class="blanktotal" colspan="3"></td>
        <td class="totals" align="left"><b>PAGOS</b></td>
        <td class="totals cost" style="color: green; font-weight: bold">(+) {{ moneyFormat($items->sum('PAGO')) }}</td>
    </tr>
    <tr>
        <td class="blanktotal" colspan="3"></td>
        <td class="totals" align="left"><b>DEDUCCIONES</b></td>
        <td class="totals cost" style="color: red; font-weight: bold">(-) {{ moneyFormat($items->sum('DEDUCCIONES')) }}</td>
    </tr>

    <tr>
        <td class="blanktotal" colspan="3"></td>
        <td class="totals" align="left"><b>NETO A PAGAR</b></td>
        <td class="totals cost" style="font-weight: bold">{{ moneyFormat($items->sum('PAGO') - $items->sum('DEDUCCIONES')) }}</td>
    </tr>
    </tbody>
</table>

@if(count($loans) > 0)
    <br>
    <br>
    <table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; border: black" cellpadding="8">
        <thead>
        <tr>
            <td colspan="5" align="center" style="font-weight: bold">PRESTAMOS</td>
        </tr>
        <tr>
            <td style="font-weight: bold">DESCRIPCIÓN</td>
            <td style="font-weight: bold">CAPITAL</td>
            <td style="font-weight: bold">VALOR CUOTA</td>
            <td style="font-weight: bold">TOTAL PAGADO</td>
            <td style="font-weight: bold">SALDO</td>
        </tr>
        </thead>
        <tbody>
            @foreach($loans as $loan)
                <tr>
                    <td align="left">{{ $loan->CODIGO_CONCEPTO.' - '.$loan->CONCEPTO }}</td>
                    <td class="cost" align="right">{{ moneyFormat($loan->VALOR) }}</td>
                    <td class="cost" align="right">{{ moneyFormat($loan->valor_cuota) }}</td>
                    <td class="cost" align="right">{{ moneyFormat($loan->VALOR - $loan->saldo) }}</td>
                    <td class="cost" align="right">{{ moneyFormat($loan->saldo) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

</body>
</html>
