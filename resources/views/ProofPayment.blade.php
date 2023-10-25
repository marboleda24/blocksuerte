<!doctype html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" href="{{ asset('dist/css/proof_payment.css') }}"></link>
    <title>Comprobante de pago electronico</title>
</head>
<body>


<div id="container">
    @php
        setlocale(LC_MONETARY, 'es_CO');
    @endphp
    <div class="flex flex-col lg:flex-row pt-10 px-5 sm:px-20 sm:pt-20 text-center sm:text-left">
        <div class="font-semibold text-theme-1 dark:text-theme-10 text-3xl">
            COMPROBANTE DE PAGO ELECTRÓNICO - TEST
        </div>
        <div class="mt-20 lg:mt-0 lg:ml-auto lg:text-right">
            <div class="text-xl text-theme-1 dark:text-theme-10 font-medium">
                CI ESTRADA VELASQUEZ Y CIA SAS
            </div>
            <div class="mt-1">NOMINA: CI ESTRADA VELASQUEZ Y CIA SAS</div>
            <div class="mt-1">{{ 'PERIODO: '.$employee->Nombres1 }}</div>
        </div>
    </div>
    <div class="flex flex-col lg:flex-row border-b px-5 sm:px-20 pt-10 pb-10 sm:pb-20 text-center sm:text-left">
        <div>
            <div class="text-base text-gray-600">EMPLEADO</div>
            <div class="text-lg font-medium text-theme-1 dark:text-theme-10 mt-2">
                {{ $employee->Nombres1 }}
            </div>
            <div class="text-base text-gray-600 mt-1">
                {{ $employee->nit }}
            </div>
        </div>

        <div class="mt-10 lg:mt-0 lg:ml-auto lg:text-right">
            <div class="text-base text-gray-600">GRUPO</div>
            <div class="text-lg text-theme-1 dark:text-theme-10 font-medium mt-2">
                {{ '# '.$contract->centro }}
            </div>
            <div class="mt-1">
                {{ $employee->Planta }}
            </div>
        </div>
    </div>
    <div class="px-5 sm:px-16 py-10 sm:py-20">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                <tr>
                    <th class="border-b-2 dark:border-dark-5 whitespace-nowrap"> DESCRIPCION</th>
                    <th class="border-b-2 dark:border-dark-5 text-center whitespace-nowrap"> CANTIDAD</th>
                    <th class="border-b-2 dark:border-dark-5 text-center whitespace-nowrap"> U.M</th>
                    <th class="border-b-2 dark:border-dark-5 text-right whitespace-nowrap"> PAGOS</th>
                    <th class="border-b-2 dark:border-dark-5 text-right whitespace-nowrap"> DEDUCCIONES</th>
                </tr>
                </thead>
                <tbody>
                    @php
                        $paid = array_reduce($items, function ($a, $c){
                                return $a + $c->PAGO;
                            }, 0);
                        $deduction = array_reduce($items, function ($a, $c){
                                return $a + $c->DEDUCCIONES;
                            }, 0);
                    @endphp
                    @foreach( $items as $item )

                    <tr>
                        <td class="border-b dark:border-dark-5">{{ $item->CONCEPTO.' - '.$item->DESCRIPCION_CONCEPTO }}</td>
                        <td class="text-center border-b dark:border-dark-5 w-32">{{ intval($item->HORAS) }}</td>
                        <td class="text-center border-b dark:border-dark-5 w-32">H</td>
                        <td class="text-right border-b dark:border-dark-5 w-32">
                            {{ floatval($item->PAGO) }}
                        </td>
                        <td class="text-right border-b dark:border-dark-5 w-32">{{  intval($item->DEDUCCIONES) }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <th class="whitespace-nowrap" colspan="3">TOTALES</th>
                        <th class="text-right whitespace-nowrap">{{ $paid }}</th>
                        <th class="text-right whitespace-nowrap">{{ $deduction }}</th>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="px-5 sm:px-20 pb-10 sm:pb-20 flex flex-col-reverse sm:flex-row">
        <div class="text-center sm:text-left mt-10 sm:mt-0">
            <div class="text-base text-gray-600">SUELDO BASICO</div>
            <div class="text-base text-gray-600 mt-1">{{ $contract->basico_mes }}</div>
        </div>
        <div class="text-center sm:text-right sm:ml-auto">
            <div class="text-base text-gray-600">NETO A PAGAR</div>
            <div class="text-xl text-theme-1 dark:text-theme-10 font-medium mt-2"> {{ $paid - $deduction }} </div>
        </div>
    </div>

    @if (count($loans) > 0)
        <div class="flex flex-col lg:flex-row pt-10 px-5 sm:px-20 sm:pt-20 text-center sm:text-left border-t">
            <div class="font-semibold text-theme-1 dark:text-theme-10 text-2xl">
                PRESTAMOS
            </div>
        </div>

        <div class="px-5 sm:px-16 py-10 sm:py-20">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                    <tr>
                        <th class="border-b-2 dark:border-dark-5 whitespace-nowrap"> DESCRIPCION</th>
                        <th class="border-b-2 dark:border-dark-5 text-right whitespace-nowrap"> CAPITAL</th>
                        <th class="border-b-2 dark:border-dark-5 text-right whitespace-nowrap"> VALOR CUOTA</th>
                        <th class="border-b-2 dark:border-dark-5 text-right whitespace-nowrap"> TOTAL PAGOS</th>
                        <th class="border-b-2 dark:border-dark-5 text-right whitespace-nowrap"> SALDO</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($loans as $loan)
                        <tr>
                            <td class="border-b dark:border-dark-5">{{ $loan->CODIGO_CONCEPTO.' - '.$loan->CONCEPTO }}</td>
                            <td class="text-right border-b dark:border-dark-5 w-32">{{ $loan->VALOR }}</td>
                            <td class="text-right border-b dark:border-dark-5 w-32">{{ $loan->valor_cuota }}</td>
                            <td class="text-right border-b dark:border-dark-5 w-32">{{ $loan->VALOR - $loan->saldo }}</td>
                            <td class="text-right border-b dark:border-dark-5 w-32">{{ $loan->saldo }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</body>
</html>
