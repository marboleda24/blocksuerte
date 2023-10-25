<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>

@foreach($job_progress as $key => $sequence)
        @if($key > 0)
            <br>
        @endif

        @if(in_array(trim($sequence->OPRSEQ_14), ['0020', '0040', '0050', '0060', '0061', '0065', '0110', '0120', '0145', '0210', '0310', '0450', '0460', '0465', '0466', '0470']))
            <table class="items" width="100%" style="font-size: 7pt; border-collapse: collapse; border: black">
                <thead>
                <tr>
                    <td rowspan="2" class="center" width="5%"># SEC</td>
                    <td rowspan="2" class="center" width="5%">CENTRO</td>
                    <td rowspan="2" class="center" width="5%">DESCRIPCIÓN</td>
                    <td rowspan="2" class="center" width="7%">MECÁNICO</td>
                    <td rowspan="2" class="center" width="7%">FECHA</td>
                    <td rowspan="2" class="center" width="10%">OPERARIO</td>
                    <td rowspan="2" class="center">TURNO</td>
                    <td rowspan="2" class="center">HORA INICIO OPERACIÓN</td>
                    <td rowspan="2" class="center">HORA FIN OPERACIÓN</td>
                    <td rowspan="2" class="center"># TROQ</td>
                    <td rowspan="2" class="center">MAQ</td>
                    <td rowspan="2" class="center">CANT REALIZADA</td>
                    <td rowspan="2" class="center">TAMAÑO MUESTRA</td>
                    <td colspan="2" class="center">NIVEL ACEPTABLE 0.65</td>
                    <td rowspan="2" class="center">TTO NC</td>
                    <td rowspan="2" class="center">TRAZABILIDAD</td>
                </tr>
                <tr>
                    <td class="center">AC</td>
                    <td class="center">RE</td>
                </tr>
                </thead>

                <tbody>
                <tr>
                    <td style="text-rotate: 90" align="center" class="center" rowspan="8">{{ $sequence->OPRSEQ_14 }}</td>
                    <td style="text-rotate: 90" align="center" class="center" rowspan="8">{{ $sequence->WRKCTR_14 }}</td>
                    <td style="text-rotate: 90" align="center" class="center" rowspan="8">{{ $sequence->OPRDES_14 }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                </tbody>
            </table>
        @else
            <table class="items" width="100%" style="font-size: 7pt; border-collapse: collapse; border: black">
                <thead>
                <tr>
                    <td rowspan="2" class="center" width="5%"># SEC</td>
                    <td rowspan="2" class="center" width="5%">CENTRO</td>
                    <td rowspan="2" class="center" width="5%">DESCRIPCIÓN</td>
                    <td rowspan="2" class="center" width="7%">FECHA</td>
                    <td rowspan="2" class="center" width="10%">PESADOR</td>
                    <td rowspan="2" class="center">CANT PESADA</td>
                    <td rowspan="2" class="center">PESO KG</td>
                    <td rowspan="2" class="center">CANT REALIZADA</td>
                    <td rowspan="2" class="center">TAMAÑO MUESTRA</td>
                    <td colspan="2" class="center">NIVEL ACEPTABLE 0.65</td>
                    <td rowspan="2" class="center">TTO NC</td>
                    <td rowspan="2" class="center">FECHA</td>
                    <td rowspan="2" class="center">SALIDA KG</td>
                    <td rowspan="2" class="center">DEVOLUCIÓN KG</td>
                </tr>
                <tr>
                    <td class="center">AC</td>
                    <td class="center">RE</td>
                </tr>
                </thead>

                <tbody>
                <tr>
                    <td style="text-rotate: 90" align="center" class="center" rowspan="6">{{ $sequence->OPRSEQ_14 }}</td>
                    <td style="text-rotate: 90" align="center" class="center" rowspan="6">{{ $sequence->WRKCTR_14 }}</td>
                    <td style="text-rotate: 90" align="center" class="center" rowspan="6">{{ $sequence->OPRDES_14 }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                </tbody>
            </table>
        @endif
    @endforeach
</body>
</html>
