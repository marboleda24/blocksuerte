<div name="myheader">
    <table width="100%" style="border-bottom: 0.5px solid rgba(25,25,26,0.71);">
        <tr>
            <td width="35%" style="color:#19191a; ">
                <span style="font-weight: bold; font-size: 12pt;">
                    HOJA DE PRODUCCION: {{ $op->OP }}
                </span> <br>
                <b>Cantidad: </b> {{ $op->Cant_Actual }}<br>
                <b>ID Pieza: </b> {{ $op->Referencia }} – {{ $op->Producto }}<br>
                <b>Pedido: </b> {{ $op->OV }} <br>
                <b>Fecha Creación: </b>{{ \Carbon\Carbon::parse($op->FechaOP)->format('Y-m-d') }}
            </td>
            <td width="25%" style="text-align: left;">
                {!!  '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG(trim($op->OP), 'C39', 1.2, 25) . '" alt="barcode"   />' !!}
                <br>
                <b>Ruta: </b> {{ $op->Ruta }}<br>
                <b>Lote: </b> <br>
                <b>Marca: </b> <br>
                <b>Fecha Impresión: </b> {{ \Carbon\Carbon::now()->format('Y-m-d H:i A') }}
            </td>

            <td width="25%" style="text-align: left;">
                <table width="100%">
                    <tr>
                        <td width="40%">
                            <table width="100%">
                                <tr>
                                    <td colspan="2" style="text-align: left; font-weight: bold;">MATERIA PRIMA:</td>
                                </tr>
                                <tr>
                                    <td>{{ $material->PRTNUM_11 }}</td>
                                    <td>{{ $material->PMDES1_01 }}</td>
                                </tr>
                                <tr>
                                    <td>CANT</td>
                                    <td>{{ round($material->CURQTY_11) }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
            <td width="20%" style="text-align: left;">
                <table width="100%" >
                    <tr>
                        <td colspan="2" style="text-align: left; font-weight: bold;">TRATAMIENTO NC:</td>
                    </tr>
                    <tr>
                        <td>1. Desecho</td>
                        <td>3. Reproceso</td>
                    </tr>

                    <tr>
                        <td>2. Revision</td>
                        <td>4. Autorización</td>
                    </tr>

                    <tr>
                        <td colspan="2">5. Devolver al proceso anterior</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
