<!DOCTYPE html>
<html>
<head>
    <title>Carta laboral goja</title>

    <style>
        .box {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            margin: auto;
            padding: 60px;
        }

        header {
            position: fixed;
            left: 0;
            right: 0;
            top: -79px;
            text-align: right;
            text-justify: auto;
        }

        body {
            background: url({{ public_path('dist/images/goja/goja_pdf.png')  }}) no-repeat 0 0;
            background-image-resize: 6;
        }

        #watermark {
            position: fixed;
            bottom: -44px;
            right: -44px;
            width: 100%;
            height: 110%;
        }

    </style>
</head>
<body>
<div id="watermark"><img src="{{ public_path('dist/images/goja/goja_pdf.png') }}" height="100%" width="100%"></div>

<main class="box">
    @php
        function format_date($date):string
        {
            $months = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
            $month = $months[($date->format('n')) - 1];
            return $date->format('d') . ' de ' . $month . ' de ' . $date->format('Y');
        }
    @endphp

    <p style="text-align: left; margin-top: 7%">
        Medellín, {{ format_date(\Carbon\Carbon::now()) }}.
    </p>

    <p style="text-align: center; margin-top: 12%; margin-bottom: 4%">
        <strong>
            A QUIEN PUEDA INTERESAR:
        </strong>
    </p>
    <br>
    <p style="text-align: justify">
        Yo, <strong>ANDRÉS FERNANDO LEMOS ARISTIZABAL</strong>, identificado con cédula de ciudadanía No. 71754539 de
        Medellin, actuando en calidad de Gerente Administrativo de <strong>PLÁSTICOS GOJA S.A.S</strong> con NIT No.
        900.349.726-2
    </p>
    <br>
    <br>
    <p style="text-align: center; margin-top: 4%; margin-bottom: 4%">
        <strong>
            DOY CONSTANCIA DE QUE:
        </strong>
    </p>
    <br>
    <p style="text-align: justify">
        {{ $employee_info->sexo === 'M' ? 'El señor' : 'La Señora' }}
        <strong>{{ $contract->nombres_apellidos }}</strong>, {{ $employee_info->sexo === 'M' ? 'identificado' : 'identificada' }}
        con cédula de ciudadanía
        No {{ $employee_info->nit }}; laboró en esta empresa desde
        el {{ format_date(\Carbon\Carbon::parse($contract->fecha_ingreso)) }} hasta
        el {{ format_date(\Carbon\Carbon::parse($contract->fecha_retiro))}}.
    </p>

    <p style="text-align: justify">
        Se desempeñaba en el cargo de {{ $employee_info->NombreOficio }}.
    </p>

    <p style="margin-top: 4%; margin-bottom: 4%">
        Cualquier información adicional con gusto la suministraremos.
    </p>

    <p style="margin-top: 8%; margin-bottom: 4%">
        Cordialmente,
    </p>
    <br>
    <p>
        <b>ANDRÉS FERNANDO LEMOS ARISTIZABAL</b> <br>
        <b>Gerente Administrativo</b>
    </p>
</main>
</body>
</html>
