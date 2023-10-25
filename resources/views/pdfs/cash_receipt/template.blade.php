<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>

<table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; border: black" cellpadding="8">
    <thead>
    @foreach($data as $key => $row) @endforeach
        @if($row->moneda === 'US')
            <tr>
                <td>#</td>
                <td>FACTURA</td>
                <td>FECHA</td>
                <td>VENCIMIENTO</td>
                <td>TOTAL</td>
                <td>ABONO</td>
                <td>SALDO</td>
                <td>SALDO EN PESOS</td>
                <td>ESTADO</td>
            </tr>

        @else
            <tr>
                <td>#</td>
                <td>FACTURA</td>
                <td>FECHA</td>
                <td>VENCIMIENTO</td>
                <td>TOTAL</td>
                <td>ABONO</td>
                <td>SALDO</td>
                <td>ESTADO</td>
            </tr>
        @endif

    </thead>
    <tbody>
    @if(count($data) > 0)
        @foreach($data as $key => $row)
            @if($row->moneda === 'US')
            <tr>
                <td align="center">{{ $key+1 }}</td>
                <td align="left">{{ $row->invoice }}</td>
                <td align="left">{{ $row->date }}</td>
                <td align="left">{{ $row->expiration }}</td>
                <td class="cost" align="right">US {{ number_format($row->total, 0, ',', '.')}}</td>
                <td class="cost" align="right">US {{ number_format($row->payments, 0, ',', '.')}}</td>
                <td class="cost" align="right">US {{  number_format($row->balance, 0, ',', '.')}}</td>
                <td class="cost" align="right">$ {{  number_format(($row->balance * $row->trm), 0, ',', '.')}}</td>
                <td align="center" style="color: {{ $row->expiration_days ? 'red' : 'green' }} ">{{ $row->expiration_days > 0 ? 'VENCIDA' : 'VIGENTE' }}</td>
            </tr>

            @else
            <tr>
                <td align="center">{{ $key+1 }}</td>
                <td align="left">{{ $row->invoice }}</td>
                <td align="left">{{ $row->date }}</td>
                <td align="left">{{ $row->expiration }}</td>
                <td class="cost" align="right">{{ number_format($row->total, 0, ',', '.') }}</td>
                <td class="cost" align="right">{{ number_format($row->payments, 0, ',', '.') }}</td>
                <td class="cost" align="right">{{ number_format($row->balance, 0, ',', '.') }}</td>
                <td align="center" style="color: {{ $row->expiration_days ? 'red' : 'green' }} ">{{ $row->expiration_days > 0 ? 'VENCIDA' : 'VIGENTE' }}</td>
            </tr>
            @endif
            @if(count($row->documents) > 0)
                <tr>
                    <td rowspan="{{ count($row->documents) + 1 }}" style="color: green" class="center">APLICACIONES</td>
                    <td style="font-weight: bold" align="center">TIPO</td>
                    <td style="font-weight: bold" align="center">NUMERO</td>
                    <td style="font-weight: bold" align="center">FECHA</td>
                    <td style="font-weight: bold" align="center" colspan="2">VALOR APLICADO</td>
                    <td style="font-weight: bold" align="center" colspan="2" rowspan="{{ count($row->documents) + 1 }}"></td>
                </tr>
                @foreach($row->documents as $payment)
                    <tr>
                        <td align="center">{{ $payment->tipo }}</td>
                        <td align="center">{{ $payment->numero }}</td>
                        <td align="center">{{ \Carbon\Carbon::parse($payment->fecha_cruce)->format('Y-m-d') }}</td>
                        <td align="right" colspan="2">{{ number_format($payment->valor, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @endif
        @endforeach

        @if($row->moneda === 'US')
        <tr>
            <td class="totals cost" align="left" colspan="4" ><b>TOTALES VENCIDAS</b></td>
            <td class="totals cost"><b>{{ number_format($data->where('expiration_days', '>', 0)->sum('total'), 0, ',', '.') }}</b></td>
            <td class="totals cost"><b>{{ number_format($data->where('expiration_days', '>', 0)->sum('payments'), 0, ',', '.') }}</b></td>
            <td class="totals cost"><b>{{ number_format($data->where('expiration_days', '>', 0)->sum('balance'), 0, ',', '.') }}</b></td>
            <td class="totals cost" align="left" colspan="4"></td>
        </tr>

        <tr>
            <td class="totals cost" align="left" colspan="4"><b>TOTALES VIGENTES</b></td>
            <td class="totals cost"><b>{{ number_format($data->where('expiration_days', '=', 0)->sum('total'), 0, ',', '.') }}</b></td>
            <td class="totals cost"><b>{{ number_format($data->where('expiration_days', '=', 0)->sum('payments'), 0, ',', '.') }}</b></td>
            <td class="totals cost"><b>{{ number_format($data->where('expiration_days', '=', 0)->sum('balance'), 0, ',', '.') }}</b></td>
            <td class="totals cost" align="left" colspan="4"></td>
        </tr>

        <tr>
            <td class="totals cost" align="left" colspan="4"><b>TOTALES</b></td>
            <td class="totals cost"><b>{{ number_format($data->sum('total'), 0, ',', '.') }}</b></td>
            <td class="totals cost"><b>{{ number_format($data->sum('payments'), 0, ',', '.') }}</b></td>
            <td class="totals cost"><b>{{ number_format($data->sum('balance'), 0, ',', '.') }}</b></td>
            <td class="totals cost" align="left" colspan="4"></td>
        </tr>

        @else
        <tr>
            <td class="totals cost" align="left" colspan="4" ><b>TOTALES VENCIDAS</b></td>
            <td class="totals cost"><b>{{ number_format($data->where('expiration_days', '>', 0)->sum('total'), 0, ',', '.') }}</b></td>
            <td class="totals cost"><b>{{ number_format($data->where('expiration_days', '>', 0)->sum('payments'), 0, ',', '.') }}</b></td>
            <td class="totals cost"><b>{{ number_format($data->where('expiration_days', '>', 0)->sum('balance'), 0, ',', '.') }}</b></td>
            <td class="totals cost" align="left" colspan="4"></td>
        </tr>

        <tr>
            <td class="totals cost" align="left" colspan="4"><b>TOTALES VIGENTES</b></td>
            <td class="totals cost"><b>{{ number_format($data->where('expiration_days', '=', 0)->sum('total'), 0, ',', '.') }}</b></td>
            <td class="totals cost"><b>{{ number_format($data->where('expiration_days', '=', 0)->sum('payments'), 0, ',', '.') }}</b></td>
            <td class="totals cost"><b>{{ number_format($data->where('expiration_days', '=', 0)->sum('balance'), 0, ',', '.') }}</b></td>
            <td class="totals cost" align="left" colspan="4"></td>
        </tr>

        <tr>
            <td class="totals cost" align="left" colspan="4"><b>TOTALES</b></td>
            <td class="totals cost"><b>{{ number_format($data->sum('total'), 0, ',', '.') }}</b></td>
            <td class="totals cost"><b>{{ number_format($data->sum('payments'), 0, ',', '.') }}</b></td>
            <td class="totals cost"><b>{{ number_format($data->sum('balance'), 0, ',', '.') }}</b></td>
            <td class="totals cost" align="left" colspan="4"></td>
        </tr>
        @endif

    @else
        <tr>
            <td class="totals cost" align="center" colspan="8" style="color: green">
                <b>CLIENTE SIN SALDOS PENDIENTES</b>
            </td>
        </tr>
    @endif

    </tbody>
</table>

</body>
</html>
