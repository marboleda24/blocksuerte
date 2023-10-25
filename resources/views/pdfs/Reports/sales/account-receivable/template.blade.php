<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>
    @php
        $flag = 0;
    @endphp
    @foreach($data as $key => $value)
        @if($flag > 0)
            <br>
            <br>
        @endif
        <table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; border: black" cellpadding="8">
            <thead>
            <tr>
                <td colspan="3" style="font-weight: bold">{{ $value[0]->razon_social }}</td>
                <td colspan="2" style="font-weight: bold">{{ $value[0]->nit }}</td>
                <td colspan="2" style="font-weight: bold">{{ strtoupper($value[0]->direccion) }}</td>
                <td colspan="2" style="font-weight: bold">{{ $value[0]->ciudad }}</td>
                <td colspan="2" style="font-weight: bold">{{ $value[0]->telefono_1 }}</td>
            </tr>
            <tr>
                <td width="6%">TIPO</td>
                <td width="7%">NUMERO</td>
                <td width="9%">FECHA</td>
                <td width="9%">VENCIMIENTO</td>
                <td width="9%">DIAS VENCIDO</td>
                <td width="10%">SALDO</td>
                <td width="10%">SIN VENCER</td>
                <td width="10%">DE 0 A 15</td>
                <td width="10%">DE 16 A 30</td>
                <td width="10%">DE 31 A 45</td>
                <td width="10%">MAS DE 45</td>
            </tr>
            </thead>
            <tbody>
                @foreach($value as $row)
                    <tr>
                        <td width="6%" align="center">{{ $row->tipo }}</td>
                        <td width="7%" align="center">{{ $row->numero }}</td>
                        <td width="9%" align="center">{{ $row->fecha }}</td>
                        <td width="9%" align="center">{{ $row->vencimiento }}</td>
                        <td width="9%" align="right">{{ $row->dias_vencido }}</td>
                        <td width="10%" class="cost" align="right">$ {{ number_format($row->saldo, 0, ',', '.') }}</td>
                        <td width="10%" class="cost" align="right">$ {{ number_format($row->sin_vencer, 0, ',', '.') }}</td>
                        <td width="10%" class="cost" align="right">$ {{ number_format($row->de_0_a_15, 0, ',', '.') }}</td>
                        <td width="10%" class="cost" align="right">$ {{ number_format($row->de_16_a_30, 0, ',', '.') }}</td>
                        <td width="10%" class="cost" align="right">$ {{ number_format($row->de_31_a_45, 0, ',', '.') }}</td>
                        <td width="10%" class="cost" align="right">$ {{ number_format($row->mas_de_45, 0, ',', '.') }}</td>
                    </tr>
                @endforeach

                <tr>
                    <td class="totals cost" align="left" colspan="5"><b>SUBTOTAL</b></td>
                    <td class="totals cost"><b>$ {{ number_format($value->sum('saldo'), 0, ',', '.') }}</b></td>
                    <td class="totals cost"><b>$ {{ number_format($value->sum('sin_vencer'), 0, ',', '.') }}</b></td>
                    <td class="totals cost"><b>$ {{ number_format($value->sum('de_0_a_15'), 0, ',', '.') }}</b></td>
                    <td class="totals cost"><b>$ {{ number_format($value->sum('de_16_a_30'), 0, ',', '.') }}</b></td>
                    <td class="totals cost"><b>$ {{ number_format($value->sum('de_31_a_45'), 0, ',', '.') }}</b></td>
                    <td class="totals cost"><b>$ {{ number_format($value->sum('mas_de_45'), 0, ',', '.') }}</b></td>
                </tr>
            </tbody>
        </table>
        @php
            $flag++;
        @endphp
    @endforeach
</body>
</html>