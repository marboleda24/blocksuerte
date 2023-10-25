<table class="table" style="width: 100%;">
    <tr>
        <th class="vertical-align-top" style="width: 50%;">
            <table style="width: 100%; margin: -5px -3px;">
                <tr>
                    <th>RECLAMACIÓN</th>
                    <th> {{$claim->consecutive}}</th>
                </tr>
                <tr>
                    <th>CLIENTE</th>
                    <th>{{$claim->invoice->RAZONSOCIAL}} </th>
                </tr>
                <tr>
                    <th>DOCUMENTO AFECTADO</th>
                    <th>{{$claim->document}}</th>
                </tr>
                <tr>
                    <th>DESTINO</th>
                    <th> {{$claim->destiny_esp}} </th>
                </tr>
                <tr>
                    <th>ACCIÓN</th>
                    <th>{{$claim->action_esp}}</th>
                </tr>
                <tr>
                    <th>MOTIVO</th>
                    <th>{{$claim->reason_esp}}</th>
                </tr>
            </table>
        </th>
        <th class="vertical-align-top" style="width: 50%;">
            <table style="width: 100%; margin: -5px -3px;">
                @if ($claim->workplace_id)
                    <tr>
                        <th>AREA RESPONSABLE</th>
                        <th>{{$claim->workplace->name}}</th>
                    </tr>
                @endif
                @if ($claim->credit_memo)
                    <tr>
                        <th>MEMO CRÉDITO</th>
                        <th>{{$claim->credit_memo}}</th>
                    </tr>
                @endif
                @if ($claim->order_id)
                    <tr>
                        <th>ORDEN DE VENTA</th>
                        <th>{{$claim->sale_order}}</th>
                    </tr>
                @endif
                @if ($claim->remission_id)
                    <tr>
                        <th>ORDEN DE VENTA</th>
                        <th>{{$claim->remission_id}}</th>
                    </tr>
                @endif
                @if ($claim->credit_note)
                    <tr>
                        <th>ORDEN DE VENTA</th>
                        <th>{{$claim->credit_note}}</th>
                    </tr>
                @endif
                @if ($claim->new_customer)
                    <tr>
                        <th>CLIENTE A FACTURAR</th>
                        <th>{{$claim->new_customer->RAZON_SOCIAL}}</th>
                    </tr>
                @endif
                <tr>
                    <th>CREADO POR</th>
                    <th>{{$claim->user->name}}</th>
                </tr>
                <tr>
                    <th>FECHA CREACIÓN</th>
                    <th>{{$claim->created_at->format('Y-m-d H:m A')}}</th>
                </tr>

                 <tr>
                    <th>ESTADO CONTABILIZACIÓN</th>
                    <th>{{$claim->accounted === '1' ? 'Contabilizado' : 'Pendiente'}}</th>
                </tr>

            </table>
        </th>
    </tr>
</table>


@if ($claim->description)
    <br>
    <table width="100%">
        <tr>
            <td style="width: 100%; text-align: left; color: #555555; border-left: 6px solid #555555" class="vertical-align-top">
                <div id="empresa-header" style="color: #555555; font-size: 14px">
                    <strong>NOTAS:</strong><br>
                </div>
                <div id="empresa-header1" style="font-size: 12px">
                    <strong>{{ $claim->notes }}</strong><br>
                </div>
            </td>
        </tr>
    </table>
@endif

<br>
<table class="table" style="width: 100%;">
    <thead>
        <tr>
            <th class="text-center">ITEM</th>
            <th class="text-center">PRODUCTO</th>
            @if($claim->reason === 'change')
                <th class="text-center">NUEVO PRODUCTO</th>
            @endif
            <th class="text-center">ARTE</th>
            <th class="text-center">MARCA</th>
            <th class="text-center">NOTAS</th>
            <th class="text-center">PRECIO</th>
            @if($claim->reason === 'price')
                <th class="text-center">NUEVO PRECIO</th>
            @endif
            <th class="text-center">CANT</th>
            @if($claim->reason === 'quantity' || $claim->reason === 'change' || $claim->reason === 'quantity-new-invoice')
                <th class="text-center">CANT. NC</th>
            @endif
            @if($claim->reason === 'quantity-new-invoice')
                <th class="text-center">CANT. FACTURAR</th>
            @endif
            @if($claim->reason === 'quantity-new-reposition')
                <th class="text-center">CANT. REPONER</th>
            @endif
            @if($claim->reason === 'NA')
                <th class="text-center">CANT. REPROCESAR</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach($claim->items as $row)
            <tr>
                <td class="text-center">{{ $row->item }}</td>
                <td class="text-left">{{ $row->product->DescripcionProducto }}</td>
                @if($claim->reason === 'change')
                    <th class="text-center">{{ $row->new_product->Descripcion }}</th>
                @endif
                <td class="text-center">{{  $row->product->ARTE }}</td>
                <td class="text-center">{{  $row->product->Marca }}</td>
                <td class="text-left">{{ $row->notes }}</td>
                <td style="text-align: right">{{ number_format($row->product->Precio, 0, ',', '.') }}</td>
                @if($claim->reason === 'price')
                    <td style="text-align: right">{{ number_format($row->new_price, 0, ',', '.') }}</td>
                @endif
                <td style="text-align: right">{{ number_format($row->product->Cantidad, 0, ',', '.') }}</td>
                @if($claim->reason === 'quantity' || $claim->reason === 'change' || $claim->reason === 'quantity-new-invoice')
                    <td style="text-align: right">{{ $row->credit_note_quantity }}</td>
                @endif
                @if($claim->reason === 'quantity-new-invoice')
                    <td style="text-align: right">{{ $row->new_quantity }}</td>
                @endif
                @if($claim->reason === 'quantity-new-reposition')
                    <td style="text-align: right">{{ $row->reposition_quantity }}</td>
                @endif
                @if($claim->reason === 'NA')
                    <td style="text-align: right">{{ $row->delivered_quantity }}</td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>

<hr style="margin-bottom: 4px; margin-top: 20px; border: 1px; color: #AAAAAA">
<br>
<br>
<table class="table" style="width: 100%;">
    <thead>
    <tr>
        <th class="text-center">USUARIO</th>
        <th class="text-center">DESCRIPCIÓN</th>
        <th class="text-center">FECHA</th>
    </tr>
    </thead>
    <tbody>
    @if($claim->quality_observation)
        <tr>
            <td>Calidad</td>
            <td>{{ $claim->quality_observation }}</td>
            <td>N/A</td>
        </tr>
    @endif

    @if($claim->cellar_observation)
        <tr>
            <td>Calidad</td>
            <td>{{ $claim->cellar_observation }}</td>
            <td>N/A</td>
        </tr>
    @endif

    @foreach($claim->logs as $log)
        @if ($log->user->id !== 175)
            <tr>
                <td class="text-left">{{ $log->user->name }}</td>
                <td class="text-left">{{ $log->description }}</td>
                <td class="text-center">{{ $log->created_at->format('d/m/Y H:m A') }}</td>
            </tr>
        @endif
    @endforeach
    </tbody>
</table>





