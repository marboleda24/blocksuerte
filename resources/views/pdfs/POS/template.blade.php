<table class="table" style="width: 100%;">
    <tr>
        <th class="vertical-align-top" style="width: 50%;">
            <table style="width: 100%; margin: -5px -3px;">
                <tr>
                    <th>VENTA #</th>
                    <th> {{$header->consecutive}}</th>
                </tr>
                <tr>
                    <th>CODIGO CLIENTE</th>
                    <th>{{$header->customer_code}} </th>
                </tr>
                <tr>
                    <th>NOMBRE CLIENTE</th>
                    <th>{{$header->customer_name}} </th>
                </tr>
            </table>
        </th>
        <th class="vertical-align-top" style="width: 50%;">
            <table style="width: 100%; margin: -5px -3px;">
                <tr>
                    <th>ALMACEN</th>
                    <th> {{$header->shop}} </th>
                </tr>
                <tr>
                    <th>REGISTRADO POR</th>
                    <th>{{$header->user_name}}</th>
                </tr>
                <tr>
                    <th>FECHA</th>
                    <th>{{$header->created_at}}</th>
                </tr>
            </table>
        </th>
    </tr>
</table>

<br>

<table class="table" style="width: 100%;">
    <thead>
    <tr>
        <th class="text-center">Codigo</th>
        <th class="text-center">Descripcion</th>
        <th class="text-center">Cantidad</th>
        <th class="text-center">Precio</th>
        <th class="text-center">Total</th>
    </tr>
    </thead>
    <tbody>
    @foreach($detail as $row)
        <tr>
            <td class="text-left">{{ $row->product_code }}</td>
            <td class="text-left">{{ $row->product_description }}</td>
            <td class="text-right" align="right">{{ number_format($row->quantity, 0, ',', '.') }}</td>
            <td class="text-right" align="right">{{ moneyFormat($row->price) }}</td>
            <td class="text-right" align="right">{{ moneyFormat($row->total) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
<table class="table" style="width: 40%; margin-left: auto; margin-top: 10px">
    <tbody>
        <tr>
            <td >BRUTO</td>
            <td align="right">{{ moneyFormat($header->bruto) }}</td>
        </tr>
        <tr>
            <td >DESCUENTO</td>
            <td align="right" style="color: red">(-) {{ moneyFormat($header->discount) }}</td>
        </tr>
        <tr>
            <td >SUBTOTAL</td>
            <td align="right">{{ moneyFormat($header->bruto - $header->discount)}}</td>
        </tr>
        <tr>
            <td >IVA</td>
            <td align="right" style="color: green">(+) {{ moneyFormat($header->taxes) }}</td>
        </tr>
        <tr>
            <td >RTE. IVA</td>
            <td align="right" style="color: red">(-) {{ moneyFormat($header->rteiva) }}</td>
        </tr>
        <tr>
            <td >RETE. FUENTE</td>
            <td align="right" style="color: red">(-) {{ moneyFormat($header->rtefuente) }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold">TOTAL</td>
            <td align="right" style="font-weight: bold">{{ moneyFormat(($header->taxes + $header->bruto) - ($header->discount + $header->rteiva + $header->rtefuente)) }}</td>
        </tr>
    </tbody>
</table>
