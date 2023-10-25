<!DOCTYPE html>
<html lang="es">
<body>
<table class="table" style="width: 100%">
    <tr>
        <th class="vertical-align-top" style="width: 50%;">
            <table style="width: 100%">
                <tr>
                    <th>CC/NIT:</th>
                    <th>{{ $invoice->IDENTIFICACION }} </th>
                </tr>
                <tr>
                    <th>Cliente:</th>
                    <th> {{ $invoice->RAZONSOCIAL }}</th>
                </tr>
                <tr>
                    <th>Dirección:</th>
                    <th>{{ $invoice->DIRECCION }}</th>
                </tr>
                <tr>
                    <th>Tipo Venta:</th>
                    <th>{{$invoice->DESCMOTIVO}}</th>
                </tr>
                <tr>
                    <th>Teléfono:</th>
                    <th>{{$invoice->TELEFONO }}</th>
                </tr>
                <tr>
                    <th>Email:</th>
                    <th>{{ $invoice->CORREO }}</th>
                </tr>
            </table>
        </th>
        <th class="vertical-align-top" style="width: 50%;">
            <table style="width: 100%">
                <tr>
                    <th>Forma de Pago:</th>
                    <th>{{$invoice->DESCPLAZO}}</th>
                </tr>
                <tr>
                    <th>Plazo Para Pagar:</th>
                    <th>{{$invoice->DIAS}} Dias</th>
                </tr>
                <tr>
                    <th>Fecha Vencimiento:</th>
                    <th> {{$invoice->VENCIMIENTO}}</th>
                </tr>
                <tr>
                    <th>Vendedor:</th>
                    <th>{{$invoice->NOMVENDEDOR}}</th>
                </tr>
                <tr>
                    <th>Orden de Compra:</th>
                    <th>{{ $invoice->OC }}</th>
                </tr>
                <tr>
                    <th>Orden Venta:</th>
                    <th>{{$invoice->OV}}</th>
                </tr>
            </table>
        </th>
    </tr>
</table>
<br><br>

<table class="table" style="width: 100%;">
    <thead>
    <tr>
        <th class="text-center">#</th>
        <th class="text-center">CÓDIGO</th>
        <th class="text-center">DESCRIPCIÓN</th>
        <th class="text-center">UM</th>
        <th class="text-center">CANT</th>
        <th class="text-center">VAL. UNIT</th>
        <th class="text-center">TOTAL</th>
    </tr>
    </thead>
    <tbody>
    <?php $ItemNro = 0; ?>
    @foreach($details as $item)
            <?php $ItemNro = $ItemNro + 1; ?>
        <tr>
            <td>{{$ItemNro}}</td>
            <td>{{$item->CodigoProducto}}</td>
            <td>
                {{$item->DescripcionProducto }}
                @php
                    $notes = $item_notes->where('Item', '=', $item->Item)
                @endphp

                @foreach($notes as $note)
                    <p style="font-style: italic; font-size: 7px"> {{ $note->Nota }}</p>
                @endforeach
            </td>
            <td class="text-right" align="right">{{$item->UM === "94" ? "Unidades" : "Mlillares" }}</td>
            <td class="text-right" align="right">{{number_format($item->Cantidad, 2)}}</td>
            <td class="text-right" align="right">{{number_format($item->Precio, 2)}}</td>
            <td class="text-right" align="right">{{number_format($item->Cantidad * $item->Precio, 2)}}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<br>

<table class="table" style="width: 100%">
    <thead>
    <tr>
        <th class="text-center"># LINEAS</th>
        <th class="text-center">BRUTO</th>
        <th class="text-center">DESCUENTOS</th>
        <th class="text-center">SUBTOTAL</th>
        <th class="text-center">IVA</th>
        <th class="text-center">TOTAL FACTURA</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="text-center">{{ $ItemNro }}</td>
        <td class="text-center">{{number_format($invoice->BRUTO, 2)}}</td>
        <td class="text-center">{{number_format($invoice->DESCUENTO, 2)}}</td>
        <td class="text-center">{{ number_format(($invoice->SUBTOTAL ), 2 ) }} </td>
        <td class="text-center">{{number_format($invoice->IVA)}}</td>
        <td class="text-center">{{number_format($invoice->SUBTOTAL+$invoice->IVA), 2}}</td>
    </tr>
    </tbody>
</table>

<br>
<div class="summarys">
    <div class="text-word" id="note">
        <p style="font-size: 10px">
            <strong>NOTAS:</strong> {{implode(' ', [$item->Comentario1, $item->Comentario2, $item->Comentario3 ])}}</p>
    </div>
</div>

</body>
</html>
