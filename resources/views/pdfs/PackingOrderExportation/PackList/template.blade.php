<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>

<table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; border: black; font-weight: bold" cellpadding="8">
    <tbody>
    <tr>
        <td width="50%">CLIENTE</td>
        <td width="50%">{{ $customer_data[0]['customer'] }}</td>
    </tr>
    <tr>
        <td width="50%">COMERCIAL</td>
        <td width="50%">{{ $customer_data[0]['seller'] }}</td>
    </tr>
    </tbody>
</table>

<br>

@foreach($customer_data as $customer)
    <table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; border: black" cellpadding="8">
        <thead>
        <tr>
            <td colspan="8" style="font-weight: bold">PEDIDO {{ $customer['order'] }}</td>
        </tr>
        <tr>
            <td>PRODUCTO</td>
            <td>NOTAS</td>
            <td>MARCA</td>
            <td>ARTE</td>
            <td>ARTE 2</td>
            <td>UM</td>
            <td>CANT</td>
            <td>PESO POR MILLAR</td>
        </tr>
        </thead>
        <tbody>
        @foreach($customer['items'] as $item)
            <tr>
                <td width="30%">{{ $item['product'] }}</td>
                <td>{{ $item['notes'] }}</td>
                <td width="10%" align="center">{{ $item['brand'] }}</td>
                <td width="8%" align="center">{{ $item['art'] }}</td>
                <td width="8%" align="center">{{ $item['art2'] }}</td>
                <td width="8%" align="center">{{ $item['um'] }}</td>
                <td width="10%" align="right">{{ $item['quantity'] }}</td>
                <td width="10%" align="right">{{ $item['weight'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

@endforeach

<br>

<table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; border: black" cellpadding="8">
    <thead>
    <tr>
        <td colspan="4" align="center" style="font-weight: bold">
            LISTA DE EMPAQUE
        </td>
    </tr>
    <tr>
        <td>CAJA</td>
        <td>TAMAÑO</td>
        <td>PRODUCTOS</td>
        <td>PESO</td>
    </tr>
    </thead>
    <tbody>
    @foreach($box_list as $key => $box)
        <tr>
            <td class="vertical-center" align="center">{{ $key+1 }}</td>
            <td>{{ $box['size'] }}</td>
            <td>
                <table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; border: black">
                    <tbody>
                        @foreach(removeDuplicates($box['products']) as $product)
                            <tr>
                                <td width="70%">{{ $product['name'] }}</td>
                                <td align="right">{{ $product['units'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
            <td class="vertical-center" align="right">{{ number_format($box['totalWeight'], 2, '.', '') }} KG</td>
        </tr>
    @endforeach
    </tbody>
</table>


</body>

@php
    function removeDuplicates($array) {
        $temp = [];

        foreach ($array as $a) {
            if (!isset($temp[$a['name']])) {
                $temp[$a['name']] = [
                    'name' => $a['name'],
                    'units' => 0
                ];
            }
            $temp[$a['name']]['units'] += intval($a['units']);
        }

        return $temp;
    }
@endphp
