<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Carta de retiro</title>
    <style>
        .box {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            margin: auto;
            padding: 60px;
        }
        @page {
            margin: 100px 1px;
        }
        header {
            position: fixed;
            left: 0;
            right: 0;
            top: -79px;
            text-align: right;
            text-justify: auto;
        }
        footer {
            position: fixed;
            left: 0;
            right: 0;
            height: 50px;
            bottom: 300px;
        }
    </style>
</head>
<body>
<header>
    <img src="{{ public_path('/dist/images/pdf_img/header.png') }}" alt="header" style=" width: 94% !important;">
</header>

<footer>
    <img src="{{ public_path('/dist/images/pdf_img/footer.png') }}" alt="header" style=" width: 101% !important;">
</footer>

<main class="box">
    @php
        function format_date($date):string
        {
            $months = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
            $month = $months[($date->format('n')) - 1];
            return $date->format('d') . ' de ' . $month . ' de ' . $date->format('Y');
        }
    @endphp

    <p style="text-align: left; margin-top: 4%">
        Medellín, {{ format_date(\Carbon\Carbon::now()) }}
    </p>

    <p style="text-align: center; margin-top: 5%; margin-bottom: 4%">
        <strong>
            A QUIEN PUEDA INTERESAR
        </strong>
    </p>

    <p style="text-align: justify">
        Yo, {{ $approve_user_info->nombres_apellidos }}, {{ $approve_user_info->sexo === 'M' ? 'identificado' : 'identificada' }} con cédula de ciudadanía No. {{ $approve_user_info->nit }} de {{ $approve_user_info->ciudad }}, actuando en calidad de {{ $approve_user_info->NombreOficio }} de C.I. ESTRADA VELASQUEZ Y CIA. S.A.S con NIT No. 890.926.617-8
    </p>

    <p style="text-align: center; margin-top: 4%; margin-bottom: 4%">
        <strong>
            DOY CONSTANCIA DE QUE:
        </strong>
    </p>

    <p style="text-align: justify">
        {{ $employee_info->sexo === 'M' ? 'El señor' : 'La Señora' }} {{ $employee_info->nombres_apellidos }}, {{ $employee_info->sexo === 'M' ? 'identificado' : 'identificada' }} con cédula de ciudadanía No. {{ $employee_info->nit }}; laboró en esta empresa desde el {{ format_date(\Carbon\Carbon::parse($contract->fecha_ingreso)) }} hasta el {{ format_date(\Carbon\Carbon::parse($contract->fecha_retiro)) }}.
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

    <p>
        <img style="width: 250px;" src="{{$signature_image}}" alt="img"/> <br>
        <b>{{ $approve_user_info->nombres }}</b> <br>
        <b>{{ $approve_user_info->NombreOficio }}</b>
    </p>
</main>
</body>
</html>
