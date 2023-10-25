<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>
<table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; border: black" cellpadding="8">
    <tbody>
    <tr>
        <td class="center" width="25%" style="font-weight: bold">CC/NIT:</td>
        <td class="center" width="25%">{{$remission->customer->NIT  }}</td>
        <td class="center" width="25%" style="font-weight: bold">CLIENTE:</td>
        <td class="center" width="25%">{{$remission->customer->RAZON_SOCIAL}}</td>
    </tr>

    <tr>
        <td class="center" width="25%" style="font-weight: bold">DIRECCIÒN: </td>
        <td class="center" width="25%">{{ $remission->customer->DIRECCION }}</td>
        <td class="center" width="25%" style="font-weight: bold">VENDEDOR(A): </td>
        <td class="center" width="25%">{{$remission->seller->name}}</td>
    </tr>

    <tr>
        <td class="center" width="25%" style="font-weight: bold">TIPO: </td>
        <td class="center" width="25%">{{$remission->type->description}}</td>
        <td class="center" width="25%" style="font-weight: bold">OC:</td>
        <td class="center" width="25%">{{$remission->oc}}</td>
    </tr>

    <tr>
        <td width="25%" style="font-weight: bold">NOTAS: </td>
        <td width="75%" colspan="3">{{ $remission->notes }}</td>
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
    @foreach($remission->detail as $item)
        <tr>
            <td class="center  ">
                {{ $item->product }}
            </td>
            <td>
                {{ $item->info->description}}
                @if(isset($item->art) || isset($item->art2) || isset($item->brand))
                    <p style="font-style: italic; font-size: 10px">
                        @if(isset($item->art) && strlen($item->art) > 0)
                            <strong>Arte: </strong>{{ $item->art }}
                        @endif

                        @if(isset($item->art) && strlen($item->art) > 0)
                            <strong>Arte 2: </strong>{{ $item->art }}
                        @endif

                        @if(isset($item->brand) && strlen($item->brand) > 0)
                            <strong>Marca: </strong> {{ $item->brand }}
                        @endif
                    </p>
                @endif
                @if(isset($item->notes) && strlen($item->notes) > 0)
                    <p style="font-style: italic; font-size: 10px"><strong>Notas: </strong> {{ $item->notes }}</p>
                @endif
            </td>
            <td class="center" align="right">{{ number_format($item->quantity, 2)}}</td>
            <td class="center" align="right">{{ moneyFormat($item->price) }}</td>
            <td class="center" align="right">{{moneyFormat(($item->quantity * $item->price))}}</td>
        </tr>
    @endforeach
    </tbody>
</table>

</body>
</html>
