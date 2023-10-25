<!DOCTYPE html>
<html lang="es">

<body>
    <table style="width: 80%;">
        <tr>
            <td class="vertical-align-top" style="width: 50%;">
                <table>
                    <tr>
                        <td>CC:</td>
                        <td>{{ $employee->nit }} </td>
                    </tr>
                    <tr>
                        <td>EMPLEADO:</td>
                        <td> {{ $employee->nombres }}</td>
                    </tr>
                </table>
            </td>

            <td class="vertical-align-top" style="width: 50%;">
                <table>
                    <tr>
                        <td>NOMINA:</td>
                        <td>PLASTICOS GOJA S.A.S </td>
                    </tr>
                    <tr>
                        <td>GRUPO:</td>
                        <td> {{ $employee->Planta }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br>

    <table class="table" style="width: 100%;">
        <thead>
            <tr>
                <th class="text-center">DESCRIPCION</th>
                <th class="text-center">CANTIDAD</th>
                <th class="text-center">UM</th>
                <th class="text-center">PAGOS</th>
                <th class="text-center">DEDUCCIONES</th>
            </tr>
        </thead>
        <tbody>
            @php
                $paid = 0;
                $deduction = 0;

                foreach($items as $key => $value){
                    if(isset($value->PAGO))
                        $paid += $value->PAGO;
                }

                foreach($items as $key=>$value){
                    if(isset($value->DEDUCCIONES))
                        $deduction += $value->DEDUCCIONES;
                }
            @endphp

            @foreach($items as $item)
                <tr>
                    <td class="text-left">{{ $item->CONCEPTO.' - '.$item->DESCRIPCION_CONCEPTO }}</td>
                    <td class="text-center">{{ intval($item->HORAS) }}</td>
                    <td class="text-center">H</td>
                    <td class="text-right" align="right">{{ moneyFormat($item->PAGO) }}</td>
                    <td class="text-right" align="right">{{ moneyFormat($item->DEDUCCIONES) }}</td>
                </tr>
            @endforeach
            <tr>
                <th colspan="3" class="text-center">
                    TOTALES
                </th>
                <td class="text-right" align="right">{{ moneyFormat($paid) }}</td>
                <td class="text-right" align="right">{{ moneyFormat($deduction) }}</td>

            </tr>
        </tbody>
    </table>
    <br>

    <table class="table" style="width: 100%">
        <thead>
            <tr>
                <th class="text-center">SUELDO BASICO</th>
                <th class="text-center">NETO A PAGAR</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">{{ moneyFormat($contract->basico_mes) }}</td>
                <td class="text-center">{{ moneyFormat($paid - $deduction) }}</td>
            </tr>
        </tbody>
    </table>

    @if(count($loans) > 0)
        <br>
        <table class="table" style="width: 100%;">
            <thead>
                <tr>
                    <th colspan="5" class="text-center">PRESTAMOS</th>
                </tr>
                <tr>
                    <th class="text-center">DESCRIPCION</th>
                    <th class="text-center">CAPITAL</th>
                    <th class="text-center">VALOR CUOTA</th>
                    <th class="text-center">TOTAL PAGADO</th>
                    <th class="text-center">SALDO</th>
                </tr>
            </thead>
            <tbody>
                @foreach( $loans as $loan )
                    <tr>
                        <td class="text-left">{{ $loan->CODIGO_CONCEPTO.' - '.$loan->CONCEPTO }}</td>
                        <td class="text-right" align="right">{{ moneyFormat($loan->VALOR) }}</td>
                        <td class="text-right" align="right">{{ moneyFormat($loan->VALOR_CUOTA) }}</td>
                        <td class="text-right" align="right">{{ moneyFormat($loan->VALOR - $loan->SALDO) }}</td>
                        <td class="text-right" align="right">{{ moneyFormat($loan->SALDO) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
