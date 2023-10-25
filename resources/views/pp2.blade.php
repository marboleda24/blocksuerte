<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Comprobante de Nomina</title>

    <style>
        .invoice-box {
            max-width: 1100px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.15);
            font-size: 8px;
            line-height: 24px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 0;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 30px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }

        /** RTL **/
        .invoice-box.rtl {
            direction: rtl;
            font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        }

        .invoice-box.rtl table {
            text-align: right;
        }

        .invoice-box.rtl table tr td:nth-child(2) {
            text-align: left;
        }
    </style>
</head>

<body>
<div class="invoice-box">
    <table cellpadding="0" cellspacing="0">
        <tr class="top">
            <td colspan="2">
                <table>
                    <tr>
                        <td class="title">
                            <img src="{{ public_path('img/ev_logo.png') }}" style="width: 100%; max-width: 100px"  alt="logo"/>
                        </td>

                        <td>
                            <h2>COMPROBANTE DE PAGO ELECTRÓNICO</h2>
                            CI ESTRADA VELASQUEZ Y CIA SAS <br />
                            {{ $period_str }}<br />
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table cellpadding="0" cellspacing="0">
        <tr class="information">
            <td colspan="2">
                <table>
                    <tr>
                        <td>
                            <strong>EMPLEADO</strong><br />
                            {{ $employee->Nombres1 }}<br />
                            {{ $employee->nit }}
                        </td>

                        <td>
                            <strong>GRUPO</strong><br />
                            {{ $employee->Planta }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table cellpadding="0" cellspacing="0">
        <tr class="heading">
            <td>DESCRIPCION</td>
            <td style="text-align: center !important;">CANTIDAD</td>
            <td style="text-align: center !important;">UM</td>
            <td align="right">PAGOS</td>
            <td align="right">DEDUCCIONES</td>
        </tr>

        @php
            $paid = 0;
            $deduction = 0;

            foreach($items as $key=>$value){
                if(isset($value->PAGO))
                    $paid += $value->PAGO;
            }

            foreach($items as $key=>$value){
                if(isset($value->DEDUCCIONES))
                    $deduction += $value->DEDUCCIONES;
            }

        @endphp

        @foreach( $items as $item )
        <tr class="item">
            <td>{{ $item->CONCEPTO.' - '.$item->DESCRIPCION_CONCEPTO }}</td>
            <td style="text-align: center !important;">{{ intval($item->HORAS) }}</td>
            <td style="text-align: center !important;">H</td>
            <td align="right">{{ moneyFormat($item->PAGO) }}</td>
            <td align="right">{{ moneyFormat($item->DEDUCCIONES) }}</td>
        </tr>
        @endforeach

        <tr class="total">
            <td colspan="2"></td>
            <td><strong>TOTALES</strong></td>
            <td align="right">{{ moneyFormat($paid) }}</td>
            <td align="right">{{ moneyFormat($deduction) }}</td>
        </tr>
        <tr class="heading" style="margin-top: 5px">
            <td colspan="3"></td>
            <td><strong>SUELDO BASICO:</strong></td>
            <td style="text-align: right !important;">{{ moneyFormat($contract->basico_mes) }}</td>
        </tr>
        <tr class="heading">
            <td colspan="3"></td>
            <td><strong>NETO A PAGAR:</strong></td>
            <td style="text-align: right !important;"> {{ moneyFormat($paid - $deduction) }}</td>
        </tr>
    </table>

    @if(count($loans) > 0)
        <div style="padding-top: 50px">
            <h2> Prestamos</h2>

            <table cellpadding="0" cellspacing="0" >
                <tr class="heading">
                    <td>DESCRIPCION</td>
                    <td style="text-align: center !important;">CAPITAL</td>
                    <td style="text-align: center !important;">VALOR CUOTA</td>
                    <td style="text-align: center !important;">TOTAL PAGADO</td>
                    <td style="text-align: center !important;">SALDO</td>
                </tr>

                @foreach( $loans as $loan )
                    <tr class="item">
                        <td>{{ $loan->CODIGO_CONCEPTO.' - '.$loan->CONCEPTO }}</td>
                        <td style="text-align: center !important;">{{ moneyFormat($loan->VALOR) }}</td>
                        <td style="text-align: center !important;">{{ moneyFormat($loan->valor_cuota) }}</td>
                        <td style="text-align: center !important;">{{ moneyFormat($loan->VALOR - $loan->saldo) }}</td>
                        <td style="text-align: center !important;">{{ moneyFormat($loan->saldo) }}</td>
                    </tr>
                @endforeach

            </table>
        </div>
    @endif


</div>
</body>
</html>
