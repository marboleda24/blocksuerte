<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>
<table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; border: black" cellpadding="8">
    <tbody>
        <tr>
            <td class="center" width="15%" style="font-weight: bold">RAZON SOCIAL</td>
            <td class="center" width="35%">{{ $order->customer->RAZON_SOCIAL }}</td>
            <td class="center" width="15%" style="font-weight: bold">PLAZO</td>
            <td class="center" width="35%">{{$order->customer->PLAZO}}</td>
        </tr>

        <tr>
            <td class="center" width="15%" style="font-weight: bold">NIT</td>
            <td class="center" width="35%">{{ $order->customer->NIT }}</td>
            <td class="center" width="15%" style="font-weight: bold">VENDEDOR(A)</td>
            <td class="center" width="35%">{{$order->seller->name}}</td>
        </tr>

        <tr>
            <td class="center" width="15%" style="font-weight: bold">DIRECCION</td>
            <td class="center" width="35%">{{ $order->customer->DIRECCION }}</td>
            <td class="center" width="15%" style="font-weight: bold">DESTINO</td>
            <td class="center" width="35%">
                {{ $order->destiny == 'C' ? 'BODEGA' : ( $order->destiny == 'P' ? 'PRODUCCION' : 'TROQUELES' )}}
            </td>
        </tr>
        <tr>
            <td width="15%" style="font-weight: bold">TELÉFONO</td>
            <td width="35%">{{ $order->customer->TEL1 }}</td>
            <td width="15%" style="font-weight: bold">TIPO</td>
            <td width="35%">
                @switch($order->type)
                    @case('national')
                        NACIONAL
                        @break

                    @case('nationalUSD')
                        NACIONAL USD
                        @break

                    @case('export')
                        EXPORTACION
                        @break

                    @case('forecast')
                        PRONOSTICO
                        @break

                    @case('samples')
                        MUESTRAS
                        @break

                    @case('elena')
                        ELENA
                        @break

                    @case('point_of_sale')
                        PUNTO DE VENTA
                        @break
                @endswitch
            </td>
        </tr>
        <tr>
            <td width="15%" style="font-weight: bold">OC</td>
            <td width="35%">{{ $order->oc }}</td>
            <td width="15%" style="font-weight: bold">NOTAS</td>
            <td width="35%">{{ $order->notes }}</td>
        </tr>
    </tbody>
</table>

<table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; border: black; margin-top: 10px" cellpadding="8">
    <thead>
    <tr>
        <td>CODIGO</td>
        <td>PRODUCTO</td>
        <td>CANTIDAD</td>
        <td>PRECIO</td>
        <td>TOTAL</td>
    </tr>
    </thead>
    <tbody>
    @foreach($order->details as $item)
        <tr>
            <td class="center">{{ $item->product }}</td>
            <td>
                <p>{{ $item->product_info->description }}</p>
                <p style="font-size: 6pt">
                    ARTE: {{ $item->art }} – MARCA: {{ $item->brand }} - CPC: {{ $item->customer_product_code }}
                </p>
                @if($item->notes)
                    <p style="font-size: 6pt">NOTAS: {{ $item->notes }}</p>
                @endif
            </td>
            <td class="center" style="text-align: right">{{ $item->quantity }}</td>
            <td class="center" style="text-align: right">{{ moneyFormat($item->price) }}</td>
            <td class="center" style="text-align: right">{{ moneyFormat($item->price * $item->quantity) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<table class="items" width="50%" style="font-size: 9pt; border-collapse: collapse; border: black; margin-left: auto" cellpadding="8">
    <tbody>
    <tr>
        <td width="50%" class="totals cost" align="left"><b>BRUTO</b></td>
        <td width="50%" class="totals cost"><b>{{ moneyFormat($order->bruto) }}</b></td>
    </tr>
    <tr>
        <td width="50%" class="totals cost" align="left"><b>DESCUENTO</b></td>
        <td width="50%" class="totals cost"><b>{{ moneyFormat($order->discount) }}</b></td>
    </tr>
    <tr>
        <td width="50%" class="totals cost" align="left"><b>SUBTOTAL</b></td>
        <td width="50%" class="totals cost"><b>{{ moneyFormat($order->subtotal) }}</b></td>
    </tr>
    <tr>
        <td width="50%" class="totals cost" align="left"><b>IVA</b></td>
        <td width="50%" class="totals cost"><b>{{ moneyFormat($order->taxes) }}</b></td>
    </tr>
    <tr>
        <td width="50%" class="totals cost" align="left"><b>TOTAL SIN RETENCIONES</b></td>
        <td width="50%" class="totals cost"><b>{{ moneyFormat($order->subtotal + $order->taxes) }}</b></td>
    </tr>
    <tr>
        <td width="50%" class="totals cost" align="left"><b>RETENCIONES</b></td>
        <td width="50%" class="totals cost"><b>{{ moneyFormat($order->retention) }}</b></td>
    </tr>
    <tr>
        <td width="50%" class="totals cost" align="left"><b>TOTAL CON RETENCIONES</b></td>
        <td width="50%" class="totals cost"><b>{{ moneyFormat(($order->subtotal + $order->taxes) - $order->retention) }}</b></td>
    </tr>
    </tbody>
</table>

</body>
</html>
