<!DOCTYPE html>
<html lang="es">
{{--<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Documento Soporte Nro: {{$support_document->consecutive}}</title>
</head> --}}
<body>
<table class="table" style="width: 100%;">
    <thead>
        <tr>
            <th>Personal natural de quien asquieren los bienes y servicios</th>
            <th>CC / NIT</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $support_document->provider_name }}</td>
            <td>{{ $support_document->provider->nit }}</td>
        </tr>
    </tbody>
</table>
<br>
<table class="table" style="width: 100%;">
    <thead>
    <tr>
        <th>Direccion</th>
        <th>Telefono</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>{{ $support_document->provider->direccion }}</td>
        <td>{{ $support_document->provider->telefono_1 }}</td>
    </tr>
    </tbody>
</table>
<br>
<table class="table" style="width: 100%;">
    <thead>
    <tr>
        <th align="center">CANT</th>
        <th align="center">DETALLE</th>
        <th align="center">VALOR UNITARIO</th>
        <th align="center">VALOR TOTAL</th>
    </tr>
    </thead>
    <tbody>
    @foreach($support_document->details as $item)
        <tr>
            <td align="center">{{ number_format($item->quantity, 0) }}</td>
            <td>{{ $item->product->description }}</td>
            <td align="right">$ {{ number_format($item->price, 2) }}</td>
            <td align="right">$ {{ number_format($item->price * $item->quantity, 2) }}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td colspan="2" style="display: none !important; visibility: collapse !important; border: none; background: transparent"></td>
        <th>SUBTOTAL</th>
        <th align="right">$ {{ number_format($support_document->bruto, 2) }}</th>
    </tr>
    <tr>
        <td colspan="2" style="display: none !important; visibility: collapse !important; border: none; background: transparent"></td>
        <th>RETEFUENTE</th>
        <th align="right">$ {{ number_format($support_document->retention, 2) }}</th>
    </tr>
    <tr>
        <td colspan="2" style="display: none !important; visibility: collapse !important; border: none; background: transparent"></td>
        <th>TOTAL</th>
        <th align="right">$ {{ number_format($support_document->bruto - $support_document->rentention, 2) }}</th>
    </tr>
    </tfoot>
</table>
</body>
