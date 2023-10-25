<!DOCTYPE html>
<html lang="es">
{{-- <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>FACTURA ELECTRONICA Nro: {{$resolution->prefix}} - {{$request->number}}</title>
</head> --}}

<body>
<table class="table" style="width: 100%;">
    <tr>
        <td class="vertical-align-top" style="width: 50%;">
            <table style="width: 100%; margin: -6px -4px -6px -4px;">
                <tr>
                    <td>Punto de venta:</td>
                    <td>{{ $remission->location }} </td>
                </tr>
                <tr>
                    <td>Consecutivo:</td>
                    <td> {{ $remission->consecutive }}</td>
                </tr>
                <tr>
                    <td>Pedido de origen:</td>
                    <td>{{ $remission->order->consecutive }}</td>
                </tr>
            </table>
        </td>
        <td class="vertical-align-top" style="width: 50%">
            <table style="width: 100%; margin: -6px -4px -6px -4px;">
                <tr>
                    <td>Creado por:</td>
                    <td>{{$remission->user->name}}</td>
                </tr>
                <tr>
                    <td>Creado él:</td>
                    <td>{{$remission->created_at}}</td>
                </tr>
                <tr>
                    <td>Estado:</td>
                    <td>
                        @if($remission->state === 'pending')
                            <div style="color: #dc2626; font-weight: bold">
                                Pendiente
                            </div>
                        @elseif($remission->state === 'transit')
                            <div style="color: #ca8a04; font-weight: bold">
                                En transito
                            </div>
                        @elseif($remission->state === 'finish')
                            <div style="color: #16a34a; font-weight: bold">
                                Mercancía recibida
                            </div>
                        @endif
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<br>

<table class="table" style="width: 100%;">
    <thead>
    <tr>
        <th class="text-center">PRODUCTO</th>
        <th class="text-center">NOTAS</th>
        <th class="text-center">UM</th>
        <th class="text-center">LOTES</th>
        <th class="text-center">CANTIDAD</th>
        <th class="text-center">PRECIO</th>
    </tr>
    </thead>
    <tbody>
    @foreach($remission->detail as $item)
        <tr>
            <td class="text-left">
                {{$item->product . " – " . trim($item->description)}}
            </td>

            <td class="text-left">
                {{$item->notes}}
            </td>

            <td class="text-left">
                {{$item->unit_measurement}}
            </td>

            <td>
                @if(isset($item->lots) && count($item->lots) > 0)
                    <p style="font-style: italic; font-size: 7px">
                        @foreach($item->lots as $lot)
                            <strong>{{$lot->name}}: {{ $lot->quantity }}</strong>
                        @endforeach
                    </p>
                @else
                    N/A
                @endif
            </td>
            <td class="text-right" align="right">{{ number_format($item->quantity, 2) }}</td>
            <td class="text-right" align="right">${{ number_format($item->price, 2) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

</body>
</html>
